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
 * Dictionary of Brevo WebHook Event Types
 */
class WebHookEvents
{
    //====================================================================//
    // Transactional Email Events
    //====================================================================//

    const string SENT            = "sent";
    const string REQUEST         = "request";
    const string DELIVERED       = "delivered";
    const string HARD_BOUNCE     = "hardBounce";
    const string SOFT_BOUNCE     = "softBounce";
    const string BLOCKED         = "blocked";
    const string SPAM            = "spam";
    const string INVALID         = "invalid";
    const string DEFERRED        = "deferred";
    const string CLICK           = "click";
    const string OPENED          = "opened";
    const string UNIQUE_OPENED   = "uniqueOpened";
    const string UNSUBSCRIBED    = "unsubscribed";

    //====================================================================//
    // Marketing Events
    //====================================================================//

    const string LIST_ADDITION   = "listAddition";

    //====================================================================//
    // Inbound Email Events
    //====================================================================//

    const string INBOUND_EMAIL_PROCESSED  = "inboundEmailProcessed";

    //====================================================================//
    // Contact Management Events
    //====================================================================//

    const string CONTACT_UPDATED  = "contactUpdated";
    const string CONTACT_DELETED  = "contactDeleted";

    //====================================================================//
    // Splash Marketing Events (used by Splash Connector)
    //====================================================================//

    const array SPLASH = array(
        self::UNSUBSCRIBED,
        self::LIST_ADDITION,
    );

    //====================================================================//
    // All Available Events
    //====================================================================//

    const array ALL = array(
        self::SENT                    => "Sent",
        self::REQUEST                 => "Request",
        self::DELIVERED               => "Delivered",
        self::HARD_BOUNCE             => "Hard Bounce",
        self::SOFT_BOUNCE             => "Soft Bounce",
        self::BLOCKED                 => "Blocked",
        self::SPAM                    => "Spam",
        self::INVALID                 => "Invalid",
        self::DEFERRED                => "Deferred",
        self::CLICK                   => "Click",
        self::OPENED                  => "Opened",
        self::UNIQUE_OPENED           => "Unique Opened",
        self::UNSUBSCRIBED            => "Unsubscribed",
        self::LIST_ADDITION           => "List Addition",
        self::INBOUND_EMAIL_PROCESSED => "Inbound Email Processed",
        self::CONTACT_UPDATED         => "Contact Updated",
        self::CONTACT_DELETED         => "Contact Deleted",
    );
}
