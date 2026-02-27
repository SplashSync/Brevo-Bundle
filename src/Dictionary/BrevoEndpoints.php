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

namespace Splash\Connectors\Brevo\Dictionary;

/**
 * Brevo Endpoints Definition
 */
class BrevoEndpoints
{
    const string LIVE = "https://api.brevo.com/v3";

    const string SANDBOX = "http://sandbox.brevo.local/v3";

    /**
     * Get Endpoint Url
     */
    public static function getEndpoint(bool $sandbox) : string
    {
        return $sandbox ? self::SANDBOX : self::LIVE;
    }
}
