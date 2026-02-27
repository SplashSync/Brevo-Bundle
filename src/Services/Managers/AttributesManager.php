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

namespace Splash\Connectors\Brevo\Services\Managers;

use Splash\Connectors\Brevo\Helpers\AttributesHelper;
use Splash\Connectors\Brevo\Models\BrevoConnectorAwareTrait;
use Splash\Core\Components\FieldsFactory;
use stdClass;
use Webmozart\Assert\Assert;

/**
 * Manage Brevo Contacts Attributes
 */
class AttributesManager
{
    use BrevoConnectorAwareTrait;

    /**
     * API Attributes Indexes Storage Key
     */
    const string ATTRIBUTES_INDEX = "ApiAttributesIndex";

    /**
     * API Attributes Details Storage Key
     */
    const string ATTRIBUTES_DETAILS = "ContactAttributes";

    /**
     * Base Attributes Metadata Item Name
     */
    const string ITEM_TYPE = "http://meta.schema.org/additionalType";

    /**
     * Get Brevo User Contacts Attributes Lists
     *
     * @return bool
     */
    public function fetchContactAttributes(): bool
    {
        //====================================================================//
        // Get User Attributes from Api
        $response = $this->getConnexion()->get('/contacts/attributes');
        if (is_null($response) || empty($response["attributes"]) || !is_array($response["attributes"])) {
            return false;
        }
        //====================================================================//
        // Parse Attributes to Connector Settings
        $attributesIndex = array();
        foreach ($response["attributes"] as $attrDetails) {
            Assert::isArray($attrDetails);
            //====================================================================//
            // Add Attribute Index
            $attributesIndex[$attrDetails["name"]] = $attrDetails["type"] ?? "text";
        }
        //====================================================================//
        // Store in Connector Settings
        $this->getConnector()->setParameter(self::ATTRIBUTES_INDEX, $attributesIndex);
        $this->getConnector()->setParameter(
            self::ATTRIBUTES_DETAILS,
            json_decode((string) json_encode($response["attributes"]))
        );
        //====================================================================//
        // Update Connector Settings
        $this->getConnector()->updateConfiguration();

        return true;
    }

    /**
     * Build Fields using FieldFactory
     *
     * @return void
     */
    public function buildAttributesFields(FieldsFactory $factory): void
    {
        //====================================================================//
        // Load Attributes List
        $attributes = $this->getConnector()->getParameter(self::ATTRIBUTES_DETAILS);
        if (!is_iterable($attributes)) {
            return;
        }

        //====================================================================//
        // Create Attributes Fields
        foreach ($attributes as $attr) {
            //====================================================================//
            // Safety Check => Attributes List was Updated to New Format
            if (!($attr instanceof stdClass) || !AttributesHelper::isAvailable($attr)) {
                continue;
            }
            //====================================================================//
            // Create Attribute Field
            $this->buildAttributeField($factory, $attr);
        }
    }

    /**
     * Find an Attribute by its Field Name
     */
    public function findByFieldName(string $fieldName): ?stdClass
    {
        //====================================================================//
        // Load Attributes List
        $attributes = $this->getConnector()->getParameter(self::ATTRIBUTES_DETAILS);
        if (empty($attributes) || !is_array($attributes)) {
            return null;
        }
        //====================================================================//
        // Walk on Contacts Attributes
        foreach ($attributes as $attr) {
            if (($attr instanceof stdClass) && strtolower($attr->name) == $fieldName) {
                return $attr;
            }
        }

        return null;
    }

    //====================================================================//
    // PRIVATE METHODS
    //====================================================================//

    /**
     * Build Field using FieldFactory
     *
     * @return void
     */
    protected function buildAttributeField(FieldsFactory $factory, stdClass $attr): void
    {
        if ($template = AttributesHelper::getTemplate($attr)) {
            //====================================================================//
            // Add Attribute to Field from Template
            $factory->createFromTemplate(strtolower($attr->name), $template);
        } else {
            //====================================================================//
            // Add Attribute to Fields
            $factory
                ->create(AttributesHelper::toSplashType($attr))
                ->identifier(strtolower($attr->name))
                ->name($attr->name)
                ->microData(self::ITEM_TYPE, strtolower($attr->name));
            ;
        }
        //====================================================================//
        // Configure Field
        $factory->group("Attributes");
        //====================================================================//
        // Add Attribute Values Choices
        foreach (AttributesHelper::getChoices($attr) as $value => $label) {
            $factory->addChoice($value, $label);
        }
    }
}
