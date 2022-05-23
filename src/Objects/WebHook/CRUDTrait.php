<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\SendInBlue\Objects\WebHook;

use Splash\Connectors\SendInBlue\Models\SendInBlueHelper as API;
use Splash\Core\SplashCore      as Splash;
use stdClass;

/**
 * SendInBlue WebHook CRUD Functions
 */
trait CRUDTrait
{
    /**
     * Load Request Object
     *
     * @param string $objectId Object id
     *
     * @return null|stdClass
     */
    public function load(string $objectId): ?stdClass
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Execute Read Request
        $sibWebHook = API::get(self::getUri($objectId));
        //====================================================================//
        // Fetch Object
        if (null == $sibWebHook) {
            return Splash::log()->errNull("Unable to load WebHook (".$objectId.").");
        }

        return $sibWebHook;
    }

    /**
     * Create Request Object
     *
     * @param null|string $url
     *
     * @return null|stdClass New Object
     */
    public function create(string $url = null): ?stdClass
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        $webhookUrl = $url ?: ($this->in["url"] ?? null);
        //====================================================================//
        // Check Customer Name is given
        if (empty($webhookUrl) || !is_string($webhookUrl)) {
            Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "url");

            return null;
        }
        //====================================================================//
        // Create Object
        $response = API::post(self::getUri(), self::getWebHooksConfiguration($webhookUrl));
        if (!($response instanceof stdClass) || empty($response->id)) {
            return Splash::log()->errNull("Unable to Create WebHook");
        }

        return $response;
    }

    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return null|string Object ID of NULL if Failed to Update
     */
    public function update(bool $needed): ?string
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        if (!$needed) {
            return $this->getObjectIdentifier();
        }

        //====================================================================//
        // Update WebHook
        if (Splash::isDebugMode()) {
            $response = API::put(self::getUri($this->object->id), $this->object);
            if (true !== $response) {
                return Splash::log()->errNull("Unable to Update WebHook (".$this->object->id.").");
            }
        }

        //====================================================================//
        // Update Not Allowed
        Splash::log()->warTrace("WebHook Update is disabled.");

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (!isset($this->object->id)) {
            return null;
        }

        return (string) $this->object->id;
    }

    /**
     * Delete requested Object
     *
     * @param string $objectId Object ID
     *
     * @return bool
     */
    public function delete(string $objectId): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Delete Object
        $response = API::delete(self::getUri($objectId));
        if (null === $response) {
            return Splash::log()->errTrace("Unable to Delete WebHook (".$objectId.").");
        }

        return true;
    }

    /**
     * Get Object CRUD Uri
     *
     * @param null|string $objectId
     *
     * @return string
     */
    private static function getUri(string $objectId = null) : string
    {
        $baseUri = 'webhooks';
        if (!is_null($objectId)) {
            return $baseUri."/".$objectId;
        }

        return $baseUri;
    }

    /**
     * Get New WebHooks Configuration
     *
     * @param string $webhookUrl
     *
     * @return stdClass
     */
    private static function getWebHooksConfiguration(string $webhookUrl) : stdClass
    {
        $webhook = new stdClass();
        $webhook->type = "marketing";
        $webhook->description = "Splash Sync WebHook";
        $webhook->url = $webhookUrl;
        $webhook->events = array("unsubscribed", "listAddition");

        return $webhook;
    }
}
