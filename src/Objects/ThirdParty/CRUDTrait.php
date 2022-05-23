<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\SendInBlue\Objects\ThirdParty;

use Splash\Connectors\SendInBlue\Models\SendInBlueHelper as API;
use Splash\Core\SplashCore      as Splash;
use stdClass;

/**
 * SendInBlue Users CRUD Functions
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
        // Get Contact Infos from Api
        $sibObject = API::get(self::getUri(self::decodeContactId($objectId)));
        if ((null == $sibObject) || !isset($sibObject->email)) {
            return Splash::log()->errNull("Unable to load Contact (".self::decodeContactId($objectId).").");
        }

        return $sibObject;
    }

    /**
     * Create Request Object
     *
     * @return null|stdClass New Object
     */
    public function create(): ?stdClass
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        //====================================================================//
        // Check Customer Name is given
        if (empty($this->in["email"]) || !is_string($this->in["email"])) {
            Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");

            return null;
        }
        //====================================================================//
        // Init Object
        $postData = array(
            'email' => $this->in["email"],
            'listIds' => array((int) API::getList()),
        );
        //====================================================================//
        // Create New Contact
        $response = API::post(self::getUri(), (object) $postData);
        if (is_null($response) || empty($response->id)) {
            return Splash::log()->errNull("Unable to Create Member (".$this->in["email"].").");
        }

        return $this->load(self::encodeContactId($this->in["email"]));
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
        //====================================================================//
        // No Update Required
        if (!$needed) {
            return $this->getObjectIdentifier();
        }

        //====================================================================//
        // Replace Contact
        if ($this->emailChanged) {
            //====================================================================//
            // Delete Contact
            $this->delete(self::encodeContactId($this->emailChanged));
            //====================================================================//
            // Create New Contact
            $response = API::post(self::getUri(), $this->object);
            if (is_null($response) || empty($response->id)) {
                return Splash::log()->errNull("Unable to Create Member (".$this->object->email.").");
            }
            //====================================================================//
            // Dispatch Object Id Updated Event
            $this->connector->objectIdChanged(
                "ThirdParty",
                self::encodeContactId($this->emailChanged),
                self::encodeContactId($this->object->email)
            );

            return $this->getObjectIdentifier();
        }

        //====================================================================//
        // Update Contact
        $response = API::put(self::getUri($this->object->email), $this->object);
        if (true !== $response) {
            return Splash::log()->errNull("Unable to Update Member (".$this->object->email.").");
        }

        return $this->getObjectIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $objectId): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        if (empty($objectId)) {
            return true;
        }
        //====================================================================//
        // Delete Contact from Api
        $result = API::delete(self::getUri(self::decodeContactId($objectId)));
        if (is_null($result)) {
            return Splash::log()->errTrace("Unable to Delete Contact (".self::decodeContactId($objectId).").");
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (!isset($this->object->email)) {
            return null;
        }

        return self::encodeContactId($this->object->email);
    }

    /**
     * Get Object CRUD Base Uri
     *
     * @param string|null $objectId
     *
     * @return string
     */
    private static function getUri(string $objectId = null) : string
    {
        $baseUri = 'contacts';
        if (!is_null($objectId)) {
            return $baseUri."/".urlencode($objectId);
        }

        return $baseUri;
    }

    /**
     * Get Object CRUD List Uri
     *
     * @param string $action
     *
     * @return string
     */
    private static function getListUri(string $action) : string
    {
        return 'contacts/lists/'.API::getList().'/contacts/'.$action;
    }
}
