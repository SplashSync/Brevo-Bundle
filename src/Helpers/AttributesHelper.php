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

use Splash\Core\Dictionary\SplFields;
use Splash\Templates\ThirdPartyFields;
use stdClass;

/**
 * Brevo Contact Attributes Helper
 */
class AttributesHelper
{
    /**
     * Get Brevo Attribute Type
     */
    public static function getType(stdClass $attribute): string
    {
        return ("category" == $attribute->category) ? "category" : $attribute->type;
    }

    /**
     * Check if this Attribute is a Phone Number Field
     */
    public static function isPhone(stdClass $attribute): bool
    {
        return in_array(strtolower($attribute->name), array("sms", "phone", "mobile"), true);
    }

    /**
     * Check if this Attribute is Available for Sync
     */
    public static function isAvailable(stdClass $attribute): bool
    {
        return in_array($attribute->category, array("normal", "category"), true);
    }

    /**
     * Get Splash Attribute Type Name
     */
    public static function toSplashType(stdClass $attribute): string
    {
        //====================================================================//
        // Special => PHONE
        if (self::isPhone($attribute)) {
            return SplFields::PHONE;
        }
        //====================================================================//
        // Map Brevo Type to Splash Type
        $attrType = self::getType($attribute);

        return match ($attrType) {
            "text" => SplFields::VARCHAR,
            "float" => SplFields::DOUBLE,
            "boolean" => SplFields::BOOL,
            "date" => SplFields::DATE,
            default => SplFields::VARCHAR,
        };
    }

    /**
     * Get Attribute Choices for Category Attributes
     *
     * @return array<string, string>
     */
    public static function getChoices(stdClass $attribute): array
    {
        //====================================================================//
        // Only Category Attributes have Choices
        if ("category" != self::getType($attribute)) {
            return array();
        }
        //====================================================================//
        // Build Choices Array
        $choices = array();
        foreach ($attribute->enumeration ?? array() as $choice) {
            if (!empty($choice->value) && !empty($choice->label)) {
                $choices[$choice->value] = sprintf("[%s] %s", $choice->value, $choice->label);
            }
        }

        return $choices;
    }

    /**
     * Get Splash Field Template for Known Brevo Attributes
     *
     * @return null|class-string
     */
    public static function getTemplate(stdClass $attribute): ?string
    {
        return match (strtolower($attribute->name)) {
            "nom" => ThirdPartyFields::FIRSTNAME,
            "prenom" => ThirdPartyFields::LASTNAME,
            "sms", "phone", "mobile" => ThirdPartyFields::MOBILE,
            default => null,
        };
    }
}
