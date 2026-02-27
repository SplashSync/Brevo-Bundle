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

namespace Splash\Connectors\Brevo\Models\Connector;

use Splash\Connectors\Brevo\Dictionary\BrevoEndpoints;
use Splash\Connectors\Brevo\Services\BrevoLocator;
use Splash\Connectors\Brevo\Services\Connexion\BrevoErrorParser;
use Splash\OpenApi\Action\Json\PutAction;
use Splash\OpenApi\Connexion\JsonConnexion;
use Splash\OpenApi\Hydrators\SymfonyHydrator;
use Splash\OpenApi\Interfaces\ConnexionInterface;
use Splash\OpenApi\Visitor\JsonVisitor;
use Webmozart\Assert\Assert;

/**
 * Brevo Open API Connector Interfaces
 */
trait BrevoApiTrait
{
    /**
     * @var array<string, ConnexionInterface>
     */
    private array $connexions = array();

    /**
     * Get Connector Api Connexion
     */
    public function getConnexion(): ConnexionInterface
    {
        $wsId = $this->getWebserviceId();
        //====================================================================//
        // Connexion already created
        if (isset($this->connexions[$wsId])) {
            return $this->connexions[$wsId];
        }
        //====================================================================//
        // Safety check
        Assert::true($this->selfTest(), "Self-test fails... Unable to create API Connexion!");
        //====================================================================//
        // Fetch Connector Configuration
        $config = $this->getConfiguration();
        //====================================================================//
        // Setup Api Connexion
        $connexion = new JsonConnexion(
            BrevoEndpoints::getEndpoint($this->isSandbox()),
            array(
                'api-key' => $config["ApiKey"]
            )
        );
        //====================================================================//
        // Setup Rate Limiter
        $connexion->setRateLimiter($this->getLocator()->getRateLimiter());
        $connexion->setErrorParser(new BrevoErrorParser());

        return $this->connexions[$wsId] = $connexion;
    }

    /**
     * Get Connector Hydrator
     */
    public function getHydrator(): SymfonyHydrator
    {
        return $this->hydrator;
    }

    /**
     * Get Brevo Connector Services Locator
     */
    public function getLocator(): BrevoLocator
    {
        return $this->locator->configure($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getVisitor(string $model): JsonVisitor
    {
        $visitor = new JsonVisitor(
            $this->getRestAdapter(),
            $this->getConnexion(),
            $this->getHydrator(),
            $model,
        );
        //====================================================================//
        // Configure Visitor
        $visitor->setTimezone("UTC");
        $visitor->setUpdateAction(PutAction::class);

        return $visitor;
    }

    /**
     * Check if we are in Sandbox Mode
     */
    public function isSandbox(): bool
    {
        return !empty($this->getParameter("isSandbox", false));
    }
}
