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

namespace Splash\Connectors\Brevo\Objects;

use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Connectors\Brevo\Connectors\BrevoConnector;
use Splash\Connectors\Brevo\Models\Api\Contact;
use Splash\Connectors\Brevo\Models\Api\Webhook;
use Splash\Core\Client\Splash;
use Splash\Core\Models\Objects\IntelParserTrait;
use Splash\Core\Models\Objects\SimpleFieldsTrait;
use Splash\OpenApi\Action\JsonLd\ListAction;
use Splash\OpenApi\Dictionary\ActionOptions;
use Splash\OpenApi\Models\Objects\AbstractRestAndMetadataObject;
use stdClass;

/**
 * Brevo Implementation of WebHooks
 */
class WebHookParser extends AbstractRestAndMetadataObject
{
    /**
     * @inheritDoc
     */
    protected static bool $disabled = false;

    /**
     * @var Webhook
     */
    protected object $object;

    /**
     * @var BrevoConnector
     */
    protected BrevoConnector $connector;

    /**
     * Class Constructor
     *
     * @param BrevoConnector $connector
     */
    public function __construct(BrevoConnector $connector)
    {
        parent::__construct(
            $visitor = $connector->getVisitor(Webhook::class),
            $visitor->getMetadataAdapter(),
            Webhook::class
        );
        //====================================================================//
        // Configure Search by Email
        $this->visitor->setListAction(ListAction::class, array(
            ActionOptions::PAGE_KEY => null,
            ActionOptions::OFFSET_KEY => "offset",
            ActionOptions::MEMBER_KEY => "webhooks",
            ActionOptions::TOTAL_KEY => "count",
        ));
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load('local');
    }

    /**
     * Override Default Mode
     *
     * @param bool $disabled
     *
     * @return void
     */
    public static function setDisabled(bool $disabled = true): void
    {
        static::$disabled = $disabled;
    }
}
