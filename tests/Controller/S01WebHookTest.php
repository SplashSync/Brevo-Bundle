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

namespace Splash\Connectors\Brevo\Test\Controller;

use Exception;
use Splash\Connectors\Brevo\Objects\ThirdParty;
use Splash\Connectors\Brevo\Services\BrevoConnector;
use Splash\Tests\Tools\TestCase;

/**
 * Test of Brevo Connector WebHook Controller
 */
class S01WebHookTest extends TestCase
{
    const PING_RESPONSE = '{"success":true}';
    const MEMBER = "ThirdParty";
    const FAKE_EMAIL = "fake@exemple.com";
    const METHOD = "JSON";

    /**
     * Test WebHook For Ping
     *
     * @throws Exception
     *
     * @return void
     */
    public function testWebhookPing(): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector("brevo");
        $this->assertInstanceOf(BrevoConnector::class, $connector);

        //====================================================================//
        // Ping Action -> POST -> KO
        $this->assertPublicActionWorks($connector, null, array("email" => "example@example.com"), "POST");
        $this->assertEquals(self::PING_RESPONSE, $this->getResponseContents());

        //====================================================================//
        // Ping Action -> POST -> KO
        $this->assertPublicActionFail($connector, null, array(), self::METHOD);
        //====================================================================//
        // Ping Action -> GET -> KO
        $this->assertPublicActionFail($connector, null, array());
        //====================================================================//
        // Ping Action -> PUT -> KO
        $this->assertPublicActionFail($connector, null, array(), "PUT");
    }

    /**
     * Test WebHook with Errors
     *
     * @throws Exception
     *
     * @return void
     */
    public function testWebhookErrors(): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector("brevo");
        $this->assertInstanceOf(BrevoConnector::class, $connector);

        //====================================================================//
        // Empty Contents
        //====================================================================//

        $this->assertPublicActionFail($connector, null, array(), self::METHOD);

        //====================================================================//
        // EVENT BUT NO EMAIL
        //====================================================================//

        $this->assertPublicActionFail($connector, null, array("event" => "unsubscribed"), self::METHOD);

        //====================================================================//
        // EMAIL BUT NO EVENT
        //====================================================================//

        $this->assertPublicActionFail($connector, null, array("email" => self::FAKE_EMAIL), self::METHOD);
    }

    /**
     * Test WebHook Member Updates
     *
     * @dataProvider webHooksInputsProvider
     *
     * @param array  $data
     * @param string $objectType
     * @param string $action
     * @param string $objectId
     *
     * @throws Exception
     *
     * @return void
     */
    public function testWebhookRequest(array $data, string $objectType, string $action, string $objectId): void
    {
        //====================================================================//
        // Load Connector
        $connector = $this->getConnector("brevo");
        $this->assertInstanceOf(BrevoConnector::class, $connector);

        //====================================================================//
        // FORM POST MODE
        $this->assertPublicActionWorks($connector, null, $data, "POST");
        $this->assertEquals(
            json_encode(array("success" => true)),
            $this->getResponseContents()
        );
        //====================================================================//
        // Verify Response
        $this->assertIsLastCommitted($action, $objectType, $objectId);

        //====================================================================//
        // JSON MODE
        $this->assertPublicActionWorks($connector, null, $data, self::METHOD);
        $this->assertEquals(
            json_encode(array("success" => true)),
            $this->getResponseContents()
        );
        //====================================================================//
        // Verify Response
        $this->assertIsLastCommitted($action, $objectType, $objectId);
    }

    /**
     * Generate Fake Inputs for WebHook Request
     *
     * @return array
     */
    public function webHooksInputsProvider(): array
    {
        $hooks = array();

        //====================================================================//
        // Generate Subscribe Events
        for ($i = 0; $i < 10; $i++) {
            //====================================================================//
            // Generate Random Contact Email
            $randEmail = uniqid().self::FAKE_EMAIL;
            //====================================================================//
            // Add WebHook Test
            $hooks[] = array(
                array(
                    "event" => "unsubscribed",
                    "email" => $randEmail,
                ),
                self::MEMBER,
                SPL_A_UPDATE,
                ThirdParty::encodeContactId($randEmail),
            );
        }

        //====================================================================//
        // Generate Add To List Events
        for ($i = 0; $i < 10; $i++) {
            //====================================================================//
            // Generate Random Contact Email
            $randEmail = uniqid().self::FAKE_EMAIL;
            //====================================================================//
            // Add WebHook Test
            $hooks[] = array(
                array(
                    "event" => "listAddition",
                    "email" => $randEmail,
                ),
                self::MEMBER,
                SPL_A_UPDATE,
                ThirdParty::encodeContactId($randEmail),
            );
        }

        return $hooks;
    }
}
