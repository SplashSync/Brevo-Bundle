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

namespace Splash\Connectors\Brevo\Objects\WebHook;

/**
 * SendInBlue WebHook Core Fields (Required)
 */
trait CoreTrait
{
    /**
     * Build Core Fields using FieldFactory
     *
     * @return void
     */
    protected function buildCoreFields(): void
    {
        //====================================================================//
        // WebHook Url
        $this->fieldsFactory()->create(SPL_T_URL)
            ->Identifier("url")
            ->Name("Url")
            ->isRequired()
            ->isListed();

        //====================================================================//
        // WebHook Description
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("description")
            ->Name("Description")
            ->isListed();
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getCoreFields($key, $fieldName): void
    {
        switch ($fieldName) {
            case 'url':
            case 'description':
                $this->getSimple($fieldName);

                break;
            default:
                return;
        }

        //====================================================================//
        // Clear Key Flag
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     *
     * @return void
     */
    protected function setCoreFields($fieldName, $fieldData): void
    {
        switch ($fieldName) {
            case 'url':
            case 'description':
                $this->setSimple($fieldName, $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}
