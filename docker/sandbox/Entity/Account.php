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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Brevo Account Entity - Stores account information.
 */
#[ORM\Entity]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(nullable: false)]
    public string $companyName;

    #[ORM\Column(nullable: false)]
    public string $email;

    #[ORM\Column(nullable: true)]
    public ?string $street = null;

    #[ORM\Column(nullable: true)]
    public ?string $zipCode = null;

    #[ORM\Column(nullable: true)]
    public ?string $city = null;

    #[ORM\Column(nullable: true)]
    public ?string $country = null;
}
