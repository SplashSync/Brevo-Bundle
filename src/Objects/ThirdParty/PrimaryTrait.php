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

namespace Splash\Connectors\SendInBlue\Objects\ThirdParty;

use Splash\Core\SplashCore as Splash;

/**
 * Manage ThirdParty Primary Search Requests
 */
trait PrimaryTrait
{
    /**
     * @inheritDoc
     */
    public function getByPrimary(array $keys): ?string
    {
        //====================================================================//
        // Safety Check
        $email = $keys['email'] ?? null;
        if (!$email) {
            return null;
        }
        //====================================================================//
        // Try to load Contact by Email
        $contactId = self::encodeContactId($email);
        $contact = $this->load($contactId);
        //====================================================================//
        // Clean Splash Log
        Splash::log()->cleanLog();

        return $contact ? $contactId : null;
    }
}
