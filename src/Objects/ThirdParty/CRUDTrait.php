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

namespace Splash\Connectors\Brevo\Objects\ThirdParty;

use Splash\Connectors\Brevo\Helpers\ContactIdHelper;
use Splash\Connectors\Brevo\Models\Api\Contact;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;
use Splash\Core\Client\Splash;
use Splash\Core\Helpers\InlineHelper;

/**
 * SendInBlue Users CRUD Functions
 */
trait CRUDTrait
{
    /**
     * @inerhitDoc
     */
    public function load(string $objectId): ?Contact
    {
        $contact = $this->coreLoad(ContactIdHelper::decode($objectId));

        return ($contact instanceof Contact) ? $contact : null;
    }

    /**
     * Create Request Object
     *
     * @return null|Contact New Contact
     */
    public function create(): ?Contact
    {
        //====================================================================//
        // Check Customer Name is given
        if (empty($this->in["email"]) || !is_string($this->in["email"])) {
            Splash::log()->err("ErrLocalFieldMissing", __CLASS__, __FUNCTION__, "email");

            return null;
        }
        //====================================================================//
        // Ensure Default Contact List is present
        $listName = $this->connector->getLocator()->getListsManager()->getDefaultListName();
        if ($listName && !Splash::isCiCdMode()) {
            $current = is_string($this->in["lists"] ?? null) ? InlineHelper::toArray($this->in["lists"]) : array();
            if (!in_array($listName, $current, true)) {
                $current[] = $listName;
            }
            $this->in["lists"] = InlineHelper::fromArray($current);
        }
        //====================================================================//
        // Create new Contact
        if (!$this->coreCreate()) {
            return Splash::log()->errNull("Unable to Create Contact (".$this->in["email"].").");
        }

        //====================================================================//
        // Load new Contact
        return $this->load(ContactIdHelper::encode($this->in["email"]));
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
            return $this->object->getId();
        }

        //====================================================================//
        // Replace Contact
        if ($this->object->hasEmailChanged()) {
            //====================================================================//
            // Delete Contact
            $this->delete(ContactIdHelper::encode((string) $this->object->getOldEmail()));
            //====================================================================//
            // Create New Contact
            $createResponse = $this->visitor->create($this->object);
            //====================================================================//
            // Verify Response
            if (!$createResponse->isSuccess()) {
                return Splash::log()->errNull("Unable to Create Member (".$this->object->email.").");
            }
            //====================================================================//
            // Dispatch Object ID Updated Event
            $this->connector->objectIdChanged(
                "ThirdParty",
                ContactIdHelper::encode((string) $this->object->getOldEmail()),
                ContactIdHelper::encode($this->object->email)
            );

            return ContactIdHelper::encode($this->object->email);
        }
        //====================================================================//
        // Update Contact
        $objectId = $this->coreUpdate(true);

        return $objectId ? ContactIdHelper::encode($objectId) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(?string $objectId = null): bool
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        if (empty($objectId)) {
            return true;
        }
        //====================================================================//
        // Load Remote Object
        $object = $this->coreLoad(ContactIdHelper::decode($objectId));
        if (empty($object)) {
            return Splash::log()->warTrace("Trying to Delete an Unknown Contact (".$objectId.").");
        }

        //====================================================================//
        // Delete Contact from Api
        return $this->visitor
            ->delete(ContactIdHelper::decode($objectId))
            ->isSuccess()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectIdentifier(): ?string
    {
        if (!isset($this->object->email)) {
            return null;
        }

        return $this->object->email;
    }

    /**
     * Get Object CRUD Base Uri
     *
     * @param null|string $objectId
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
