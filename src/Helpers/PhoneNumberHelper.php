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

namespace Splash\Connectors\Brevo\Helpers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Phone Number Normalization Helper
 *
 * Converts phone numbers to E.164 international format using libphonenumber.
 */
class PhoneNumberHelper
{
    /**
     * Default Region for Phone Number Parsing (ISO 3166-1 alpha-2)
     */
    private static string $defaultRegion = "FR";

    /**
     * Configure Default Region for Phone Number Parsing
     */
    public static function setDefaultRegion(string $region): void
    {
        self::$defaultRegion = $region;
    }

    /**
     * Get Default Region for Phone Number Parsing
     */
    public static function getDefaultRegion(): string
    {
        return self::$defaultRegion;
    }

    /**
     * Convert a Phone Number to E.164 International Format
     *
     * @param null|string $phone Raw phone number
     *
     * @return null|string Phone number in E.164 format, or raw value if parsing fails
     */
    public static function toInternational(?string $phone): ?string
    {
        //====================================================================//
        // Skip Empty Values
        if (empty($phone)) {
            return $phone;
        }

        try {
            //====================================================================//
            // Parse & Validate Phone Number
            $phoneUtil = PhoneNumberUtil::getInstance();
            $parsed = $phoneUtil->parse($phone, self::$defaultRegion);
            if (!$phoneUtil->isValidNumber($parsed)) {
                return $phone;
            }

            //====================================================================//
            // Format to E.164
            return $phoneUtil->format($parsed, PhoneNumberFormat::E164);
        } catch (NumberParseException) {
            //====================================================================//
            // Parsing Failed => Return Raw Value (let Brevo reject it)
            return $phone;
        }
    }
}
