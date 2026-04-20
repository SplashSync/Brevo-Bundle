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

use Splash\Connectors\Brevo\DataTransformers\AttributeTransformer;

/**
 * SendInBlue Contacts Attributes Fields (Required)
 */
trait AttributesTrait
{
    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    protected function buildAttributesFields(): void
    {
        //====================================================================//
        // Load Attributes List from Attributes Manager
        $this->connector->getLocator()
            ->getAttributesManager()
            ->buildAttributesFields($this->fieldsFactory())
        ;
    }

    /**
     * Read Requested Field
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
        $attr = $this->connector->getLocator()->getAttributesManager()->findByFieldName($fieldName);
        if (!$attr) {
            return;
        }
        $attrName = $attr["name"] ?? null;
        if (!is_string($attrName)) {
            return;
        }
        //====================================================================//
        // Read & Transform Attribute Value
        $rawValue = $this->object->attributes[$attrName] ?? null;
        $this->out[$fieldName] = AttributeTransformer::toSplash($attr, $rawValue);
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
        $attr = $this->connector->getLocator()->getAttributesManager()->findByFieldName($fieldName);
        if (!$attr) {
            return;
        }
        $attrName = $attr["name"] ?? null;
        if (!is_string($attrName)) {
            return;
        }
        //====================================================================//
        // Transform Splash Value to Brevo Value
        $brevoValue = AttributeTransformer::toBrevo($attr, $fieldData);
        //====================================================================//
        // Compare & Update Attribute Value
        $origin = $this->object->attributes[$attrName] ?? null;
        if ($origin != $brevoValue) {
            $this->object->attributes[$attrName] = $brevoValue;
            $this->needUpdate();
        }

        unset($this->in[$fieldName]);
    }
}
