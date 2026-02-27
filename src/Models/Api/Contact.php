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

namespace Splash\Connectors\Brevo\Models\Api;

use Splash\Connectors\Brevo\Helpers\ContactIdHelper;
use Splash\Connectors\Brevo\Models\Api\Common\AuditTrait;
use Splash\Connectors\Brevo\Models\Api\Contact\EmailTrait;
use Splash\Metadata\Attributes as SPL;
use Splash\OpenApi\Dictionary\SerializerGroups as SplGroups;
use Splash\Templates\ThirdPartyFields;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Json Metadata Model for Brevo Contacts.
 */
#[SPL\SplashObject(
    type: "ThirdParty",
    name: "Customer",
    description: "Brevo Contact",
    ico: "fa fa-user",
)]
class Contact
{
    use EmailTrait;
    use AuditTrait;

    /**
     * Brevo Contact ID
     */
    #[Serializer\Groups(array(SplGroups::READ, SplGroups::LIST))]
    public int $id;

    /**
     * Email Opt-Out Flag - Blocks all email communications
     */
    #[SPL\Template(ThirdPartyFields::NO_EMAIL)]
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public bool $emailBlacklisted = false;

    /**
     * SMS Opt-Out Flag - Blocks all SMS communications
     */
    #[SPL\Template(ThirdPartyFields::NO_SMS)]
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public bool $smsBlacklisted = false;

    /**
     * Contact Custom Attributes (Dynamic Fields)
     */
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public array $attributes = array("default" => "");

    /**
     * Contact Mailing Lists IDs (Current / To Add))
     */
    #[Assert\All(
        new Assert\Type("int"),
    )]
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public array $listIds = array();

    /**
     * Contact Mailing Lists IDs (To Remove)
     */
    #[Assert\All(
        new Assert\Type("int"),
    )]
    #[Serializer\Groups(SplGroups::WRITE)]
    public array $unlinkListIds = array();

    //====================================================================//
    // Getters & Setters
    //====================================================================//

    /**
     * Get Encoded Contact ID
     */
    public function getId(): string
    {
        return ContactIdHelper::encode($this->email);
    }

    public function getAttributes(): array
    {
        return !empty($this->attributes) ? $this->attributes : array("default" => "none");
    }
}
