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

use Splash\Connectors\Brevo\Connectors\BrevoConnector;
use Splash\Connectors\Brevo\Dictionary\WebHookEvents;
use Splash\Connectors\Brevo\Models\Api\Webhook;
use Splash\Core\Client\Splash;
use Splash\Core\Helpers\InlineHelper;
use Splash\OpenApi\Action\JsonLd\ListAction;
use Splash\OpenApi\Dictionary\ActionOptions;
use Splash\OpenApi\Models\Objects\AbstractRestAndMetadataObject;

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

    /**
     * Create Splash WebHook from Url
     */
    public function createFromUrl(string $url): bool
    {
        return !empty($this->set(null, array(
            "type" => "marketing",
            "description" => "Splash Sync WebHook",
            "url" => $url,
            "events" => InlineHelper::fromArray(array(
                WebHookEvents::UNSUBSCRIBED,
                WebHookEvents::LIST_ADDITION,
            ))
        )));
    }
}
