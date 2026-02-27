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
use App\Controller\Contact\CreateController;
use App\Controller\Contact\ListingController;
use App\Controller\Contact\UpdateController;
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
        new API\GetCollection(
            controller: ListingController::class,
            read: false,
        ),
        new API\Post(
            controller: CreateController::class,
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
        new API\Put(
            requirements: array('email' => '.+\\..+'),
            status: 204,
            controller: UpdateController::class,
            output: false,
            read: false,
            deserialize: false,
            write: false,
        ),
        new API\Delete(requirements: array('email' => '.+\\..+'), status: 204, output: false),
    )
)]
class Contact
{
    use Traits\AuditTrait;

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
    private array $listIds = array();

    private array $unlinkListIds = array();

    public function __construct()
    {
        $this->initAudit();
    }

    //====================================================================//
    // Brevo-Like Getters & Setters
    //====================================================================//

    public function getListIds(): array
    {
        return $this->listIds;
    }

    /**
     * Add list IDs to current lists (Brevo merge behavior).
     */
    public function setListIds(array $listIds): void
    {
        $this->listIds = array_values(array_unique(array_merge(
            $this->listIds,
            $listIds
        )));
    }

    /**
     * Remove list IDs from current lists (Brevo unlink behavior).
     */
    public function setUnlinkListIds(array $unlinkListIds): void
    {
        $this->listIds = array_values(array_diff(
            $this->listIds,
            $unlinkListIds
        ));
    }
}
