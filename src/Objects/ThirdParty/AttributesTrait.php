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

use stdClass;

/**
 * SendInBlue Contacts Attributes Fields (Required)
 */
trait AttributesTrait
{
    /**
     * Collection of Known Attributes Names with Spacial Mapping
     *
     * This Collection is Public to Allow External Additons
     *
     * @var array
     */
    public static $knowAttributes = array(
        "nom" => array("http://schema.org/Person", "familyName"),
        "prenom" => array("http://schema.org/Person", "givenName"),
        "sms" => array("http://schema.org/Person", "telephone"),
    );

    /**
     * Attributes Type <> Splash Type Mapping
     *
     * @var array
     */
    private static $attrType = array(
        "text" => SPL_T_VARCHAR,
        "float" => SPL_T_DOUBLE,
        "boolean" => SPL_T_BOOL,
        "date" => SPL_T_DATE,
    );

    /**
     * Base Attributes Metadata Item Name
     *
     * @var string
     */
    private static $baseProp = "http://meta.schema.org/additionalType";

    private $attrCache;

    /**
     * Build Fields using FieldFactory
     */
    protected function buildAttributesFields()
    {
        //====================================================================//
        // Load Attributes List
        $attributes = $this->getParameter("ContactAttributes");
        if (!is_iterable($attributes)) {
            return;
        }

        //====================================================================//
        // Create Attributes Fields
        $factory = $this->fieldsFactory();
        foreach ($attributes as $attr) {
            //====================================================================//
            // Safety Check => Attributes List was Updated to New Format
            if (!($attr instanceof stdClass)) {
                continue;
            }
            //====================================================================//
            // Attributes Not Used
            if (!$this->isAvailable($attr)) {
                continue;
            }
            //====================================================================//
            // Add Attribute to Fields
            $factory
                ->create(self::toSplashType($attr))
                ->Identifier(strtolower($attr->name))
                ->Name($attr->name)
                ->Group("Attributes");

            //====================================================================//
            // Add Attribute MicroData
            $attrCode = strtolower($attr->name);
            if (isset(static::$knowAttributes[$attrCode])) {
                $factory->MicroData(
                    static::$knowAttributes[$attrCode][0],
                    static::$knowAttributes[$attrCode][1]
                );

                continue;
            }
            $factory->MicroData(static::$baseProp, strtolower($attr->name));
        }
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getAttributesFields($key, $fieldName)
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
            switch ($attr->type) {
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
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    protected function setAttributesFields($fieldName, $fieldData)
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
        $origin = null;
        if (isset($this->object->attributes->{$attrName})) {
            $origin = $this->object->attributes->{$attrName};
        }
        //====================================================================//
        // Compare & Update Attribute Value
        if ($origin != $fieldData) {
            $this->object->attributes->{$attrName} = $fieldData;
            $this->needUpdate();
        }

        unset($this->in[$fieldName]);
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
            $this->attrCache = $this->getParameter("ContactAttributes");
            if (empty($this->attrCache) || !is_iterable($this->attrCache)) {
                return null;
            }
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
    private function isAvailable($attribute)
    {
        if ("normal" == $attribute->category) {
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
    private static function toSplashType($attribute)
    {
        //====================================================================//
        // Special => PHONE
        if ("SMS" == $attribute->name) {
            return SPL_T_PHONE;
        }
        //====================================================================//
        // From mapping
        if (isset(static::$attrType[$attribute->type])) {
            return static::$attrType[$attribute->type];
        }
        //====================================================================//
        // Default Type
        return SPL_T_VARCHAR;
    }
}
