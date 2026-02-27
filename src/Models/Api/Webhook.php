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

use Splash\Connectors\Brevo\Dictionary\WebHookEvents;
use Splash\Connectors\Brevo\Models\Api\Common\AuditTrait;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\InlineHelper;
use Splash\Metadata\Attributes as SPL;
use Splash\OpenApi\Dictionary\SerializerGroups as SplGroups;
use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Json Metadata Model for Brevo WebHooks.
 */
#[SPL\SplashObject(
    type: "Webhook",
    name: "WebHook",
    description: "Brevo WebHook",
    ico: "fa fa-plug",
)]
class Webhook
{
    use AuditTrait;

    /**
     * WebHook ID
     */
    #[Serializer\Groups(SplGroups::ALL)]
    public int $id;

    /**
     * WebHook Endpoint URL
     */
    #[Assert\NotBlank]
    #[Assert\Url]
    #[SPL\Field(
        type: SplFields::URL,
        name: "Url",
        desc: "WebHook endpoint URL",
    )]
    #[SPL\Flags(required: true, listed: true)]
    #[Serializer\Groups(SplGroups::ALL)]
    public string $url = '';

    /**
     * WebHook Description
     */
    #[SPL\Field(
        type: SplFields::VARCHAR,
        name: "Description",
        desc: "WebHook description",
    )]
    #[SPL\Flags(listed: true)]
    #[Serializer\Groups(SplGroups::DEFAULT_LISTED)]
    public ?string $description = null;

    /**
     * WebHook Type (marketing or transactional)
     */
    #[SPL\Field(
        type: SplFields::VARCHAR,
        name: "Type",
        desc: "WebHook type",
    )]
    #[SPL\Choices(array(
        "marketing" => "Marketing",
        "transactional" => "Transactional",
        "inbound" => "Inbound",
    ))]
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public ?string $type = 'marketing';

    /**
     * WebHook Triggered Events
     */
    #[Assert\All(
        new Assert\Type("string"),
    )]
    #[SPL\Field(
        type: SplFields::INLINE,
        name: "Events",
        desc: "WebHook events",
    )]
    #[SPL\Choices(WebHookEvents::ALL)]
    #[SPL\Accessor(getter: 'getInlineEvents', setter: 'setInlineEvents')]
    #[Serializer\Groups(SplGroups::DEFAULT)]
    public array $events = array();

    /**
     * Get Events as Splash Inline String
     */
    public function getInlineEvents(): string
    {
        return InlineHelper::fromArray($this->events);
    }

    /**
     * Set Events from Splash Inline String
     */
    public function setInlineEvents(?string $events): void
    {
        $this->events = InlineHelper::toArray($events);
    }
}
