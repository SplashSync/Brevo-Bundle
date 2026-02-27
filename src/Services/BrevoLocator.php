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

namespace Splash\Connectors\Brevo\Services;

use Psr\Container\ContainerInterface;
use Splash\Connectors\Brevo\Models\BrevoConnectorAwareTrait;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Webmozart\Assert\Assert;

class BrevoLocator implements ServiceSubscriberInterface
{
    use BrevoConnectorAwareTrait;

    public function __construct(
        private ContainerInterface $locator,
    ) {
    }

    public static function getSubscribedServices(): array
    {
        return array(
            Managers\ListsManager::class,
            Managers\AttributesManager::class,
            Connexion\BrevoRateLimiter::class,
        );
    }

    //====================================================================//
    // Access to Configured Services
    //====================================================================//

    /**
     * Get Brevo Lists Manager
     */
    public function getListsManager(): Managers\ListsManager
    {
        Assert::isInstanceOf(
            $service = $this->locator->get(Managers\ListsManager::class),
            Managers\ListsManager::class
        );

        return $service->configure($this->connector);
    }

    /**
     * Get Brevo Attributes Manager
     */
    public function getAttributesManager(): Managers\AttributesManager
    {
        Assert::isInstanceOf(
            $service = $this->locator->get(Managers\AttributesManager::class),
            Managers\AttributesManager::class
        );

        return $service->configure($this->connector);
    }

    /**
     * Get Brevo API Rate Limiter
     */
    public function getRateLimiter(): Connexion\BrevoRateLimiter
    {
        Assert::isInstanceOf(
            $service = $this->locator->get(Connexion\BrevoRateLimiter::class),
            Connexion\BrevoRateLimiter::class
        );

        return $service;
    }
}
