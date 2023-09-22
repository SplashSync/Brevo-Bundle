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

use stdClass;

/**
 * SendInBlue Contacts Attributes Fields (Required)
 */
trait AttributesTrait
{
    /**
     * Collection of Known Attributes Names with Spacial Mapping
     *
     * This Collection is Public to Allow External Additions
     *
     * @var array
     */
    public static array $knowAttributes = array(
        "nom" => array("http://schema.org/Person", "familyName"),
        "prenom" => array("http://schema.org/Person", "givenName"),
        "sms" => array("http://schema.org/Person", "telephone"),
    );

    /**
     * Attributes Type <> Splash Type Mapping
     *
     * @var array
     */
    private static array $attrType = array(
        "text" => SPL_T_VARCHAR,
        "float" => SPL_T_DOUBLE,
        "boolean" => SPL_T_BOOL,
        "date" => SPL_T_DATE,
        "category" => SPL_T_VARCHAR,
    );

    /**
     * Base Attributes Metadata Item Name
     *
     * @var string
     */
    private static string $baseProp = "http://meta.schema.org/additionalType";

    /**
     * @var null|array
     */
    private ?array $attrCache;

    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    protected function buildAttributesFields(): void
    {
        //====================================================================//
        // Load Attributes List
        $attributes = $this->getParameter("ContactAttributes");
        if (!is_iterable($attributes)) {
            return;
        }

        //====================================================================//
        // Create Attributes Fields
        foreach ($attributes as $attr) {
            //====================================================================//
            // Safety Check => Attributes List was Updated to New Format
            if (!($attr instanceof stdClass) || !$this->isAvailable($attr)) {
                continue;
            }
            //====================================================================//
            // Create Attribute Field
            $this->buildAttributeField($attr);
        }
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     *
     * @return void
     */
    protected function getAttributesFields(string $key, string $fieldName): void
    {
        //====================================================================//
        // Field is not an Attribute
        $attr = $this->isAttribute($fieldName);
        if (!$attr) {
            return;
        }
        //====================================================================//
        // Attribute Not Defined
        $fieldData = null;
        $attrName = $attr->name;
        if (isset($this->object->attributes->{$attrName})) {
            //====================================================================//
            // Extract Attribute Value
            switch (self::getSibType($attr)) {
                case 'float':
                    $fieldData = (float) $this->object->attributes->{$attrName};

                    break;
                default:
                    $fieldData = $this->object->attributes->{$attrName};

                    break;
            }
        }
        //====================================================================//
        // Store Value
        $this->out[$fieldName] = $fieldData;
        //====================================================================//
        // Clear Key Flag
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string                $fieldName Field Identifier / Name
     * @param null|float|int|string $fieldData Field Data
     *
     * @return void
     */
    protected function setAttributesFields(string $fieldName, null|string|float|int $fieldData): void
    {
        //====================================================================//
        // Field is not an Attribute
        $attr = $this->isAttribute($fieldName);
        if (!$attr) {
            return;
        }
        //====================================================================//
        // Fetch Original Attribute Value
        $attrName = $attr->name;
        $origin = $this->object->attributes->{$attrName} ?? null;
        //====================================================================//
        // Convert Splash Value to SendInBlue Value
        $fieldData = self::getSibValue($attr, $fieldData);
        //====================================================================//
        // Compare & Update Attribute Value
        if ($origin != $fieldData) {
            $this->object->attributes->{$attrName} = $fieldData;
            $this->needUpdate();
        }

        unset($this->in[$fieldName]);
    }

    /**
     * Build Field using FieldFactory
     *
     * @return void
     */
    protected function buildAttributeField(stdClass $attr): void
    {
        $factory = $this->fieldsFactory();
        //====================================================================//
        // Add Attribute to Fields
        $factory
            ->create(self::toSplashType($attr))
            ->identifier(strtolower($attr->name))
            ->name($attr->name)
            ->group("Attributes")
        ;
        //====================================================================//
        // Add Attribute Values Choices
        if ("category" == self::getSibType($attr)) {
            foreach ($attr->enumeration ?? array() as $choice) {
                if (!empty($choice->value) && !empty($choice->label)) {
                    $factory->addChoice($choice->value, sprintf("[%s] %s", $choice->value, $choice->label));
                }
            }
        }
        //====================================================================//
        // Add Attribute MicroData
        $attrCode = strtolower($attr->name);
        if (isset(static::$knowAttributes[$attrCode])) {
            $factory->microData(
                self::$knowAttributes[$attrCode][0],
                self::$knowAttributes[$attrCode][1]
            );

            return;
        }
        $factory->microData(self::$baseProp, strtolower($attr->name));
    }

    /**
     * Check if this Attribute Exists
     *
     * @param string $fieldName
     *
     * @return null|stdClass
     */
    private function isAttribute(string $fieldName) : ?stdClass
    {
        //====================================================================//
        // Safety Check => Attributes Are Loaded
        if (empty($this->attrCache)) {
            $attributes = $this->getParameter("ContactAttributes");
            if (empty($attributes) || !is_array($attributes)) {
                return null;
            }
            $this->attrCache = $attributes;
        }
        //====================================================================//
        // Walk On Contacts Attributes
        foreach ($this->attrCache as $attr) {
            if (strtolower($attr->name) == $fieldName) {
                return $attr;
            }
        }

        return null;
    }

    /**
     * Check if this Attribute is To Sync
     *
     * @param stdClass $attribute
     *
     * @return bool
     */
    private function isAvailable(stdClass $attribute): bool
    {
        if (in_array($attribute->category, array("normal", "category"), true)) {
            return true;
        }

        return false;
    }

    /**
     * Get Splash Attribute Type Name
     *
     * @param stdClass $attribute
     *
     * @return string
     */
    private static function toSplashType(stdClass $attribute): string
    {
        //====================================================================//
        // Special => PHONE
        if ("SMS" == $attribute->name) {
            return SPL_T_PHONE;
        }
        $attrType = self::getSibType($attribute);
        //====================================================================//
        // From mapping
        if (isset(self::$attrType[$attrType])) {
            return self::$attrType[$attrType];
        }

        //====================================================================//
        // Default Type
        return SPL_T_VARCHAR;
    }

    /**
     * Get SendInBlue Attribute Type
     *
     * @param stdClass $attribute
     *
     * @return string
     */
    private static function getSibType(stdClass $attribute): string
    {
        return ("category" == $attribute->category) ? "category" : $attribute->type;
    }

    /**
     * Convert Splash Value to SendInBlue Value
     *
     * @param stdClass              $attribute
     * @param null|float|int|string $value
     *
     * @return null|float|int|string
     */
    private static function getSibValue(stdClass $attribute, null|string|float|int $value): null|string|float|int
    {
        //====================================================================//
        // Detect Category Value
        if ("category" == self::getSibType($attribute)) {
            //====================================================================//
            // Find by Value
            foreach ($attribute->enumeration ?? array() as $choice) {
                if (!empty($choice->value) && ($choice->value == $value)) {
                    return $choice->value;
                }
            }
            //====================================================================//
            // Find by Label
            foreach ($attribute->enumeration ?? array() as $choice) {
                if (!empty($choice->value) && !empty($choice->label) && ($choice->label == $value)) {
                    return $choice->value;
                }
            }
        }

        //====================================================================//
        // Use Raw Value
        return $value;
    }
}
