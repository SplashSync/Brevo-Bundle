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

namespace App\Entity\Traits;

use ApiPlatform\Metadata as API;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Audit fields for Brevo API Sandbox entities.
 */
trait AuditTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[API\ApiProperty(writable: false)]
    public DateTime $modifiedAt;

    public function initAudit(): void
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