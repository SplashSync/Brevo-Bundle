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

namespace Splash\Connectors\Brevo\DataTransformers;

use Splash\Connectors\Brevo\Helpers\AttributesHelper;
use Splash\Connectors\Brevo\Helpers\PhoneNumberHelper;

/**
 * Transform Brevo Contact Attribute Values between Splash and Brevo Formats
 */
class AttributeTransformer
{
    /**
     * Convert Brevo Attribute Value to Splash Value
     *
     * @param null|float|int|string $rawValue Raw value from Brevo API
     */
    public static function toSplash(array $attribute, null|string|float|int $rawValue): null|string|float|int
    {
        if (null === $rawValue) {
            return null;
        }

        return match (AttributesHelper::getType($attribute)) {
            "float" => (float) $rawValue,
            default => $rawValue,
        };
    }

    /**
     * Convert Splash Value to Brevo Attribute Value
     *
     * @param null|float|int|string $value Splash field value
     */
    public static function toBrevo(array $attribute, null|bool|string|float|int $value): null|bool|string|float|int
    {
        $type = AttributesHelper::getType($attribute);
        //====================================================================//
        // Category Attributes => Resolve by Value or Label
        if ("category" == $type) {
            return self::resolveCategoryValue($attribute, (string) $value);
        }
        //====================================================================//
        // Phone Attributes => Normalize to E.164 International Format
        if (AttributesHelper::isPhone($attribute) && is_string($value)) {
            return PhoneNumberHelper::toInternational($value);
        }

        //====================================================================//
        // Use Raw Value
        return match ($type) {
            "boolean" => !empty($value),
            default => $value,
        };
    }

    //====================================================================//
    // PRIVATE METHODS
    //====================================================================//

    /**
     * Resolve Category Attribute Value from Value or Label
     *
     * Value match takes priority over Label match.
     */
    private static function resolveCategoryValue(
        array $attribute,
        null|string|float|int $value
    ): null|string|float|int {
        $labelMatch = null;
        foreach ($attribute["enumeration"] ?? array() as $choice) {
            if (!is_array($choice)) {
                continue;
            }
            $choiceValue = $choice["value"] ?? null;
            if (!is_string($choiceValue) && !is_int($choiceValue) && !is_float($choiceValue)) {
                continue;
            }
            //====================================================================//
            // Direct Match on Value => Return Immediately
            if ($choiceValue == $value) {
                return $choiceValue;
            }
            //====================================================================//
            // Match on Label => Remember but Keep Scanning for a Value Match
            if (!empty($choice["label"]) && ($choice["label"] == $value)) {
                $labelMatch ??= $choiceValue;
            }
        }

        return $labelMatch ?? $value;
    }
}
