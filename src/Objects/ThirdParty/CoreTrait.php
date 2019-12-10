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

namespace Splash\Connectors\SendInBlue\Objects\ThirdParty;

/**
 * SendInBlue ThirdParty Core Fields (Required)
 */
trait CoreTrait
{
    /**
     * @var false|string
     */
    protected $emailChanged = false;

    /**
     * Build Core Fields using FieldFactory
     *
     * @return void
     */
    protected function buildCoreFields(): void
    {
        //====================================================================//
        // Email
        $this->fieldsFactory()->create(SPL_T_EMAIL)
            ->Identifier("email")
            ->Name("Email")
            ->MicroData("http://schema.org/ContactPoint", "email")
            ->isRequired()
            ->isListed()
            ->isNotTested();

        //====================================================================//
        // Excluded from Email Campaigns
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("emailBlacklisted")
            ->Name("Is Exluded from Emails Campaigns")
            ->MicroData("http://schema.org/Organization", "excluded")
            ->isListed();

        //====================================================================//
        // Excluded from SMS Campaigns
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("smsBlacklisted")
            ->Name("Is Exluded from Sms Campaigns")
            ->MicroData("http://schema.org/Organization", "excludedSms")
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
            case 'email':
            case 'emailBlacklisted':
            case 'smsBlacklisted':
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
            case 'email':
                if ($this->object->email != strtolower($fieldData)) {
                    //====================================================================//
                    //  Mark for Update Object Id In DataBase
                    $this->emailChanged = $this->object->email;
                    //====================================================================//
                    //  Update Field Data
                    $this->object->email = $fieldData;
                    $this->needUpdate();
                }

                break;
            case 'emailBlacklisted':
            case 'smsBlacklisted':
                $this->setSimple($fieldName, $fieldData ? true : false);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}
