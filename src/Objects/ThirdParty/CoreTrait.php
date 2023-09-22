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
            ->identifier("email")
            ->name("Email")
            ->microData("http://schema.org/ContactPoint", "email")
            ->isRequired()
            ->isPrimary()
            ->isListed()
            ->isNotTested()
        ;
        //====================================================================//
        // Excluded from Email Campaigns
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("emailBlacklisted")
            ->name("Is Exluded from Emails Campaigns")
            ->microData("http://schema.org/Organization", "excluded")
            ->isListed()
        ;
        //====================================================================//
        // Excluded from SMS Campaigns
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("smsBlacklisted")
            ->name("Is Exluded from Sms Campaigns")
            ->microData("http://schema.org/Organization", "excludedSms")
            ->isListed()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getCoreFields(string $key, string $fieldName): void
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
     * @param string           $fieldName Field Identifier / Name
     * @param null|bool|string $fieldData Field Data
     *
     * @return void
     */
    protected function setCoreFields(string $fieldName, bool|string|null $fieldData): void
    {
        switch ($fieldName) {
            case 'email':
                if ($this->object->email != strtolower((string) $fieldData)) {
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
                $this->setSimple($fieldName, (bool) $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }
}
