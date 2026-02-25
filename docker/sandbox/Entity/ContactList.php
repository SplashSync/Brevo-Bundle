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
use App\Controller\ContactListController;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Brevo Contact List Entity - Stores mailing list information.
 *
 * GET single: handled natively by API Platform.
 * List: custom controller (wrapped format).
 */
#[ORM\Entity]
#[API\ApiResource(
    uriTemplate: '/v3/contacts/lists',
    operations: array(
        new API\GetCollection(
            controller: ContactListController::class,
            read: false,
        ),
    )
)]
#[API\ApiResource(
    uriTemplate: '/v3/contacts/lists/{id}',
    operations: array(new API\Get())
)]
class ContactList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(nullable: false)]
    public string $name;

    #[ORM\Column(type: Types::INTEGER)]
    public int $totalSubscribers = 0;
}
