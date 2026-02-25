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
use App\Controller\ContactController;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Brevo Contact Entity - Stores contact/subscriber information.
 *
 * POST: custom controller (duplicate_parameter error format).
 * GET/PUT/DELETE: handled natively by API Platform.
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[API\ApiResource(
    uriTemplate: '/v3/contacts',
    operations: array(
        new API\Post(
            controller: ContactController::class,
            read: false,
            deserialize: false,
            write: false,
        ),
    )
)]
#[API\ApiResource(
    uriTemplate: '/v3/contacts/{email}',
    operations: array(
        new API\Get(requirements: array('email' => '.+\\..+')),
        new API\Put(requirements: array('email' => '.+\\..+')),
        new API\Delete(requirements: array('email' => '.+\\..+')),
    )
)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[API\ApiProperty(identifier: false)]
    public int $id;

    #[ORM\Column(unique: true, nullable: false)]
    #[API\ApiProperty(identifier: true)]
    public string $email;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $emailBlacklisted = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $smsBlacklisted = false;

    #[ORM\Column(type: Types::JSON)]
    public array $attributes = array();

    #[ORM\Column(type: Types::JSON)]
    public array $listIds = array();

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $modifiedAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $createdAt;

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
