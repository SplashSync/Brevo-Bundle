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

use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\InlineHelper;

/**
 * Brevo Contacts List Fields
 */
trait ListsTrait
{
    /**
     * Build Contact List Fields using FieldFactory
     */
    protected function buildListFields(): void
    {
        $listManager = $this->connector->getLocator()->getListsManager();

        $this->fieldsFactory()->create(SplFields::INLINE, "lists")
            ->name("Lists")
            ->description("List to which the contact belongs")
            ->setPreferRead()
            ->addChoices($listManager->getChoices())
        ;
    }

    /**
     * Read Contact Lists Names
     */
    protected function getListFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Filter on List Field
        if ($fieldName != 'lists') {
            return;
        }
        $listManager = $this->connector->getLocator()->getListsManager();
        //====================================================================//
        // Convert List ID to List Name
        $names = array_filter(array_map(
            fn ($listId) => $listManager->getName($listId),
            $this->object->listIds
        ));
        $this->out[$fieldName] = InlineHelper::fromArray($names);
        //====================================================================//
        // Clear Key Flag
        unset($this->in[$key]);
    }

    /**
     * Write Contact Lists Names
     */
    protected function setListFields(string $fieldName, null|string $fieldData): void
    {
        //====================================================================//
        // Filter on List Field
        if ($fieldName != 'lists') {
            return;
        }
        $listManager = $this->connector->getLocator()->getListsManager();
        //====================================================================//
        // Convert List Names to List ID
        $listIds = array_filter(array_map(
            fn ($listName) => $listManager->getIndex($listName),
            InlineHelper::toArray($fieldData)
        ));
        //====================================================================//
        // Compute Lists Changes
        $newIds = array_values($listIds);
        $oldIds = $this->object->listIds;
        $toAdd = array_values(array_diff($newIds, $oldIds));
        $toRemove = array_values(array_diff($oldIds, $newIds));
        //====================================================================//
        // Apply Lists Changes
        if (!empty($toAdd) || !empty($toRemove)) {
            $this->object->listIds = $toAdd;
            $this->object->unlinkListIds = $toRemove;
            $this->needUpdate();
        }

        unset($this->in[$fieldName]);
    }

}
