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

namespace Splash\Connectors\Brevo\Services\Managers;

use Splash\Bundle\Services\ConnectorRoutesBuilder;
use Splash\Connectors\Brevo\Models\BrevoConnectorAwareTrait;
use Splash\Connectors\Brevo\Objects;

/**
 * Manage Brevo WebHooks Registration
 */
class WebHookManager
{
    use BrevoConnectorAwareTrait;

    public function __construct(
        private readonly ConnectorRoutesBuilder $routesBuilder,
    ) {
    }

    /**
     * Check if Splash WebHook is Registered
     */
    public function verify(): bool
    {
        //====================================================================//
        // Generate WebHook Url
        $webHookUrl = $this->routesBuilder->getMasterActionUrl($this->getConnector());
        //====================================================================//
        // Get List Of WebHooks
        $webHooks = $this->fetchWebHooksList();
        //====================================================================//
        // Search for Splash WebHook
        foreach ($webHooks as $webHook) {
            //====================================================================//
            // Skip Non-Splash WebHooks
            if (!$this->routesBuilder->isSplashUrl($webHook['url'])) {
                continue;
            }
            //====================================================================//
            // This is our Splash WebHook
            if (trim($webHook['url']) == $webHookUrl) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register or Update Splash WebHook
     */
    public function update(): bool
    {
        //====================================================================//
        // Generate WebHook Url
        $webHookUrl = $this->routesBuilder->getMasterActionUrl($this->getConnector());
        //====================================================================//
        // Get List Of WebHooks
        $webHooks = $this->fetchWebHooksList();
        //====================================================================//
        // Filter & Clean List Of WebHooks
        $foundWebHook = false;
        foreach ($webHooks as $webHook) {
            //====================================================================//
            // This is Current Node WebHook
            if (trim($webHook['url']) == $webHookUrl) {
                $foundWebHook = true;

                continue;
            }
            //====================================================================//
            // This is an Old Splash WebHook => Delete
            if ($this->routesBuilder->isSplashUrl($webHook['url'])) {
                $this->getWebHookParser()->delete($webHook['id']);
            }
        }
        //====================================================================//
        // Splash WebHook Already Registered
        if ($foundWebHook) {
            return true;
        }

        //====================================================================//
        // Register New Splash WebHook
        return (false !== $this->getWebHookParser()->create($webHookUrl));
    }

    //====================================================================//
    // Private Methods
    //====================================================================//

    /**
     * Get Configured WebHook Parser
     */
    private function getWebHookParser(): Objects\WebHookParser
    {
        $connector = $this->getConnector();
        $webHookParser = new Objects\WebHookParser($connector);
        $webHookParser->configure("webhook", $connector->getWebserviceId(), $connector->getConfiguration());

        return $webHookParser;
    }

    /**
     * Fetch All Marketing WebHooks
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchWebHooksList(): array
    {
        $webHooks = $this->getWebHookParser()->objectsList();
        if (isset($webHooks["meta"])) {
            unset($webHooks["meta"]);
        }

        return $webHooks;
    }
}
