<?php

namespace Splash\Connectors\Brevo\Models\Api;

use DateTime;
use Splash\Connectors\Brevo\Helpers\ContactIdHelper;
use Splash\Connectors\Brevo\Models\Api\Contact\EmailTrait;
use Splash\Core\Dictionary\SplFields;
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

    /**
     * Brevo Contact ID
     */
    #[Serializer\Groups(SplGroups::ALL)]
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
    public array $attributes = array();

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

    /**
     * Contact Last Modification Date
     */
    #[SPL\Template(ThirdPartyFields::DATE_CREATED)]
    #[Serializer\Groups(SplGroups::READ)]
    public DateTime $modifiedAt;

    /**
     * Contact Creation Date
     */
    #[SPL\Template(ThirdPartyFields::DATE_MODIFIED)]
    #[Serializer\Groups(SplGroups::READ)]
    public DateTime $createdAt;

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
}