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

namespace Splash\Connectors\Brevo\Models\Api\Contact;

use Splash\Metadata\Attributes as SPL;
use Splash\OpenApi\Dictionary\SerializerGroups as SplGroups;
use Splash\Templates\ThirdPartyFields;
use Symfony\Component\Serializer\Attribute as Serializer;

/**
 * Brevo Contact Email Management
 */
trait EmailTrait
{
    /**
     * Contact Email Address
     */
    #[SPL\Template(ThirdPartyFields::EMAIL)]
    #[SPL\IsPrimary]
    #[SPL\IsRequired]
    #[Serializer\Groups(SplGroups::ALL)]
    public string $email;

    /**
     * Previous Email Address (before update)
     */
    private ?string $oldEmail = null;

    /**
     * Get Previous Email Address
     */
    public function getOldEmail(): ?string
    {
        return $this->oldEmail;
    }

    /**
     * Set Email Address and Track Previous Value
     */
    public function setEmail(string $email): static
    {
        if (isset($this->email) && $this->email !== $email) {
            $this->oldEmail = $this->email;
        }
        $this->email = $email;

        return $this;
    }

    /**
     * Check if Email Address has Changed
     */
    public function hasEmailChanged(): bool
    {
        return null !== $this->oldEmail && $this->oldEmail !== $this->email;
    }
}
