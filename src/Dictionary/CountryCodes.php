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

namespace Splash\Connectors\Brevo\Dictionary;

use Symfony\Component\Intl\Countries;

/**
 * ISO 3166-1 Alpha-2 Country Codes for Phone Number Formatting
 */
class CountryCodes
{
    /**
     * Connector Configuration Key for Default Country
     */
    const string CONFIG_KEY = "ApiDfCountry";

    /**
     * Get Available Country Codes as Choices for Form Select
     *
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        //====================================================================//
        // Build Choices from Symfony Intl: "Country Name" => "XX"
        $choices = array();
        foreach (Countries::getNames() as $code => $name) {
            $choices[$name] = $code;
        }

        return $choices;
    }
}
