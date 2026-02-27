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

namespace Splash\Connectors\Brevo\Models;

use Splash\Connectors\Brevo\Connectors\BrevoConnector;
use Splash\OpenApi\Interfaces\ConnexionInterface;

/**
 * Makes any Service Aware of Brevo Connector
 */
trait BrevoConnectorAwareTrait
{
    /**
     * Currently Used Brevo Connector
     */
    private BrevoConnector $connector;

    /**
     * Configure with Current Connector
     */
    public function configure(BrevoConnector $connector): static
    {
        $this->connector = $connector;

        return $this;
    }

    /**
     * Get Connector
     */
    public function getConnector(): BrevoConnector
    {
        return $this->connector;
    }

    /**
     * Get Connexion
     */
    public function getConnexion(): ConnexionInterface
    {
        return $this->getConnector()->getConnexion();
    }
}
