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

namespace Splash\Connectors\Brevo\Services\Managers;

use Splash\Connectors\Brevo\Models\BrevoConnectorAwareTrait;

/**
 * Manage Brevo Contacts Lists
 */
class ListsManager
{
    use BrevoConnectorAwareTrait;

    /**
     * Default API List Index
     */
    const string DEFAULT_INDEX = "ApiList";

    /**
     * API List Indexes Storage Key
     */
    const string LISTS_INDEX = "ApiListsIndex";

    /**
     * API List Details Storage Key
     */
    const string LISTS_DETAILS = "ApiListsDetails";

    /**
     * Get SendInBlue User Lists
     *
     * @return bool
     */
    public function fetchMailingLists(): bool
    {
        //====================================================================//
        // Get User Lists from Api
        $response = $this->getConnexion()->get('/contacts/lists');
        if (is_null($response) || empty($response["lists"]) || !is_array($response["lists"])) {
            return false;
        }
        //====================================================================//
        // Parse Lists to Connector Settings
        $listIndex = array();
        foreach ($response["lists"] as $listDetails) {
            //====================================================================//
            // Add List Index
            $listIndex[$listDetails["id"]] = $listDetails["name"];
        }
        //====================================================================//
        // Store in Connector Settings
        $this->getConnector()->setParameter(self::LISTS_INDEX, $listIndex);
        $this->getConnector()->setParameter(self::LISTS_DETAILS, $response["lists"]);
        //====================================================================//
        // Update Connector Settings
        $this->getConnector()->updateConfiguration();

        return true;
    }

    /**
     * Get Default Contact List Index
     */
    public function getDefaultListIndex(): ?string
    {
        $index = $this->getConnector()->getParameter(self::DEFAULT_INDEX);

        return is_string($index) ? $index : null;
    }

    /**
     * Get List Name from ID
     */
    public function getName(int $listId): ?string
    {
        $index = $this->getConnector()->getParameter(self::LISTS_INDEX);
        if (!is_array($index) || !isset($index[$listId])) {
            return null;
        }

        return (string) $index[$listId];
    }

    /**
     * Get List ID from Name
     */
    public function getIndex(string $listName): ?int
    {
        $index = $this->getConnector()->getParameter(self::LISTS_INDEX);
        if (!is_array($index)) {
            return null;
        }
        //====================================================================//
        // Search by Name
        $listId = array_search($listName, $index, true);

        return (false !== $listId) ? (int) $listId : null;
    }

    /**
     * Get All Lists as Choices Array
     *
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        $index = $this->getConnector()->getParameter(self::LISTS_INDEX);
        if (!is_array($index)) {
            return array();
        }

        return array_combine(array_values($index), array_values($index));
    }
}
