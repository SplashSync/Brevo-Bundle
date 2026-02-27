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

namespace Splash\Connectors\Brevo\Connectors;

use ArrayObject;
use Psr\Log\LoggerInterface;
use Splash\Bundle\Interfaces\ConnectorInterface;
use Splash\Bundle\Interfaces\Connectors\PrimaryKeysInterface;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\Connectors\GenericObjectMapperTrait;
use Splash\Bundle\Models\Connectors\GenericObjectPrimaryMapperTrait;
use Splash\Bundle\Models\Connectors\GenericWidgetMapperTrait;
use Splash\Bundle\Models\Connectors\RoutesBuilderAwareTrait;
use Splash\Bundle\Services\ConnectorRoutesBuilder;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;
use Splash\Connectors\Brevo\Models\Connector\BrevoApiTrait;
use Splash\Connectors\Brevo\Models\Connector\BrevoProfileTrait;
use Splash\Connectors\Brevo\Objects;
use Splash\Connectors\Brevo\Services\BrevoLocator;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;
use Splash\OpenApi\Hydrators\SymfonyHydrator;
use Splash\OpenApi\Models\Connector\RestAdapterAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Brevo REST API Connector for Splash
 */
#[AutoconfigureTag(ConnectorInterface::TAG)]
class BrevoConnector extends AbstractConnector implements PrimaryKeysInterface
{
    use RestAdapterAwareTrait;
    use GenericObjectMapperTrait;
    use GenericObjectPrimaryMapperTrait;
    use GenericWidgetMapperTrait;
    use RoutesBuilderAwareTrait;
    use BrevoProfileTrait;
    use BrevoApiTrait;

    /**
     * Objects Type Class Map
     *
     * @var array<string, class-string>
     */
    protected static array $objectsMap = array(
        "ThirdParty" => Objects\ThirdParty::class,
        "WebHook" => Objects\WebHookParser::class,
    );

    /**
     * Widgets Type Class Map
     *
     * @var array<string, class-string>
     */
    protected static array $widgetsMap = array(
        "SelfTest" => "Splash\\Connectors\\Brevo\\Widgets\\SelfTest",
    );

    /**
     * Class Constructor
     */
    public function __construct(
        private readonly SymfonyHydrator   $hydrator,
        private readonly BrevoLocator       $locator,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ConnectorRoutesBuilder $routesBuilder,
    ) {
        parent::__construct($eventDispatcher, $logger);
        $this->setRouteBuilder($routesBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function ping() : bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Perform Ping Test
        $this->getConnexion()->get("/account");
        //====================================================================//
        // Check Response
        $response = $this->getConnexion()->getLastResponse();
        if ($response && ($response->code >= 200) && ($response->code < 500)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function connect() : bool
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Perform Ping Test
        $this->getConnexion()->get("/account");
        //====================================================================//
        // Check Response
        $response = $this->getConnexion()->getLastResponse();
        if (!$response || (200 != $response->code)) {
            return false;
        }
        //====================================================================//
        // Get List of Available Lists
        if (!$this->getLocator()->getListsManager()->fetchMailingLists()) {
            return false;
        }
        //====================================================================//
        // Get List of Available Members Properties
        if (!$this->getLocator()->getAttributesManager()->fetchContactAttributes()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function informations(ArrayObject  $informations) : ArrayObject
    {
        //====================================================================//
        // Server General Description
        $informations->shortdesc = "Brevo";
        $informations->longdesc = "Splash Integration for Brevo's Api V3.0";
        //====================================================================//
        // Server Logo & Ico
        $informations->icoraw = Splash::file()->readFileContents(
            dirname(__FILE__, 2)."/Resources/public/img/Brevo-Icon.png"
        );
        $informations->logourl = null;
        $informations->logoraw = Splash::file()->readFileContents(
            dirname(__FILE__, 2)."/Resources/public/img/Brevo-Logo.png"
        );
        //====================================================================//
        // Server Information
        $informations->servertype = "Brevo REST Api V3";
        $informations->serverurl = API::ENDPOINT;
        //====================================================================//
        // Module Information
        $informations->moduleauthor = SplDefinition::AUTHOR;
        $informations->moduleversion = "master";

        $config = $this->getConfiguration();
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest() || empty($config["ApiList"])) {
            return $informations;
        }
        //====================================================================//
        // Get List Detailed Information
        $details = $this->getConnexion()->get("/account");
        if (is_null($details)) {
            return $informations;
        }
        //====================================================================//
        // Company Information
        $informations->company = $details["companyName"];
        $informations->address = $details["address"]["street"];
        $informations->zip = $details["address"]["zipCode"];
        $informations->town = $details["address"]["city"];
        $informations->country = $details["address"]["country"];
        $informations->www = "www.sendinblue.com";
        $informations->email = $details['email'];
        $informations->phone = "~";

        return $informations;
    }

    /**
     * {@inheritdoc}
     */
    public function selfTest() : bool
    {
        $config = $this->getConfiguration();

        //====================================================================//
        // Verify Api Key is Set
        //====================================================================//
        if (empty($config["ApiKey"]) || !is_string($config["ApiKey"])) {
            Splash::log()->err("Api Key is Invalid");

            return false;
        }

        //====================================================================//
        // SandBox Mode
        //====================================================================//
        if (!$this->isSandbox()) {
            Objects\WebHookParser::setDisabled();
        }

        //====================================================================//
        // Build Sandbox Endpoint if needed
        $endpoint = null;
        if ($this->getParameter("isSandbox", false)) {
            $endpoint = rtrim($config["WsHost"] ?? '', '/')."/v3/";
        }

        //====================================================================//
        // Configure Rest API
        return API::configure(
            $config["ApiKey"],
            $config["ApiList"] ?? null,
            $endpoint
        );
    }

    //====================================================================//
    // Files Interfaces
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getFile(string $filePath, string $fileMd5): ?array
    {
        //====================================================================//
        // Safety Check => Verify Self-test Pass
        if (!$this->selfTest()) {
            return null;
        }
        Splash::log()->err("There are No Files Reading for Brevo Up To Now!");

        return null;
    }
}
