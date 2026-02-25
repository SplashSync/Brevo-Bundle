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

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Controller\WebHookController;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Brevo WebHook Entity - Stores webhook configuration.
 *
 * POST/GET/PUT/DELETE: handled natively by API Platform.
 * List: custom controller (wrapped format).
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[API\ApiResource(
    uriTemplate: '/v3/webhooks',
    operations: array(
        new API\Post(),
        new API\GetCollection(
            controller: WebHookController::class,
            read: false,
        ),
    )
)]
#[API\ApiResource(
    uriTemplate: '/v3/webhooks/{id}',
    operations: array(new API\Get(), new API\Put(), new API\Delete())
)]
class WebHook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(nullable: false)]
    public string $url = '';

    #[ORM\Column(nullable: true)]
    public ?string $description = null;

    #[ORM\Column(nullable: true)]
    public ?string $type = 'marketing';

    #[ORM\Column(type: Types::JSON)]
    public array $events = array();

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $modifiedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->modifiedAt = new DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->modifiedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->modifiedAt = new DateTime();
    }
}
