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

namespace Splash\Connectors\Brevo\Helpers;

/**
 * Brevo Contact ID Encoder/Decoder
 */
class ContactIdHelper
{
    /**
     * Encode Contact Email to Splash ID String
     */
    public static function encode(string $email): string
    {
        return base64_encode(strtolower($email));
    }

    /**
     * Decode Contact Email from Splash Id String
     */
    public static function decode(string $contactId): string
    {
        return (string) base64_decode($contactId, true);
    }
}
