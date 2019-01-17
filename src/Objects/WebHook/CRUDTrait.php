<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
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
     * @return mixed
     */
    public function load($objectId)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Execute Read Request
        $sibWebHook = API::get(self::getUri($objectId));
        //====================================================================//
        // Fetch Object
        if (null == $sibWebHook) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to load WebHook (".$objectId.").");
        }
        
        return $sibWebHook;
    }

    /**
     * Create Request Object
     *
     * @param string $url
     *
     * @return false|stdClass New Object
     */
    public function create(string $url = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Check Customer Name is given
        if (empty($url) && empty($this->in["url"])) {
            return Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "url");
        }
        $webhookUrl = empty($url) ? $this->in["Url"] : $url;
        //====================================================================//
        // Create Object
        $response = API::post(self::getUri(), self::getWebHooksConfiguration($webhookUrl));
        if (is_null($response) || !($response instanceof stdClass) || empty($response->id)) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to Create WebHook");
        }

        return $response;
    }
    
    /**
     * Update Request Object
     *
     * @param bool $needed Is This Update Needed
     *
     * @return false|string Object Id of False if Failed to Update
     */
    public function update(bool $needed)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        if (!$needed) {
            return (string) $this->object->id;
        }
        
        //====================================================================//
        // Update WebHook
        if (true == SPLASH_DEBUG) {
            $response = API::put(self::getUri($this->object->id), $this->object);
            if (true !== $response) {
                return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to Update WebHook (".$this->object->id.").");
            }
        }
        
        //====================================================================//
        // Update Not Allowed
        Splash::log()->war("ErrLocalTpl", __CLASS__, __FUNCTION__, " WebHook Update is disabled.");
        
        return (string) $this->object->id;
    }
    
    /**
     * Delete requested Object
     *
     * @param string $objectId Object Id
     *
     * @return bool
     */
    public function delete($objectId = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Delete Object
        $response = API::delete(self::getUri($objectId));
        if (null === $response) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to Delete WebHook (".$objectId.").");
        }

        return true;
    }
    
    /**
     * Get Object CRUD Uri
     *
     * @param string $objectId
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
//        $webhook->events = array("delivered", "click", "opened", "unsubscribed", "listAddition");
        $webhook->events = array("unsubscribed", "listAddition");

        return $webhook;
    }
}
