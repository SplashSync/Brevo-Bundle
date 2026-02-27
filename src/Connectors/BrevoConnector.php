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
use Splash\Connectors\Brevo\Dictionary\BrevoEndpoints;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;
use Splash\Connectors\Brevo\Models\Connector\BrevoProfileTrait;
use Splash\Connectors\Brevo\Objects;
use Splash\Connectors\Brevo\Services\BrevoLocator;
use Splash\Connectors\Brevo\Services\Connexion\BrevoErrorParser;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplDefinition;
use Splash\OpenApi\Action\Json\PutAction;
use Splash\OpenApi\Connexion\JsonConnexion;
use Splash\OpenApi\Hydrators\SymfonyHydrator;
use Splash\OpenApi\Interfaces\ConnexionInterface;
use Splash\OpenApi\Models\Connector\RestAdapterAwareTrait;
use Splash\OpenApi\Visitor\JsonVisitor;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

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

    /**
     * Objects Type Class Map
     *
     * @var array<string, class-string>
     */
    protected static array $objectsMap = array(
        "ThirdParty" => Objects\ThirdParty::class,
        "WebHook" => Objects\WebHook::class,
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
     * @var array<string, ConnexionInterface>
     */
    private array $connexions = array();

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
        $details = API::get('account');
        if (is_null($details)) {
            return $informations;
        }

        //====================================================================//
        // Company Information
        $informations->company = $details->companyName;
        $informations->address = $details->address->street;
        $informations->zip = $details->address->zipCode;
        $informations->town = $details->address->city;
        $informations->country = $details->address->country;
        $informations->www = "www.sendinblue.com";
        $informations->email = $details->email;
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
        // Extended Mode
        //====================================================================//
        if ($this->getParameter("Extended", false)) {
            Objects\WebHook::setDisabled(false);
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
    // Open API Connector Interfaces
    //====================================================================//

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
     * @return SymfonyHydrator
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

    //====================================================================//
    // Objects Interfaces
    //====================================================================//

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

    //====================================================================//
    //  HIGH LEVEL WEBSERVICE CALLS
    //====================================================================//

    /**
     * Check & Update SendInBlue Api Account WebHooks.
     *
     * @return bool
     */
    public function verifyWebHooks() : bool
    {
        //====================================================================//
        // Connector SelfTest
        if (!$this->selfTest()) {
            return false;
        }

        //====================================================================//
        // Generate WebHook Url
        $webHookUrl = $this->routeBuilder->getMasterActionUrl($this);
        //====================================================================//
        // Create Object Class
        $webHookManager = new Objects\WebHook($this);
        $webHookManager->configure("webhook", $this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Get List Of WebHooks for this List
        $webHooks = $webHookManager->objectsList();
        if (isset($webHooks["meta"])) {
            unset($webHooks["meta"]);
        }
        //====================================================================//
        // Filter & Clean List Of WebHooks
        foreach ($webHooks as $webHook) {
            //====================================================================//
            // This is a Splash WebHooks
            if (!$this->getRouteBuilder()->isSplashUrl($webHook['url'])) {
                continue;
            }
            //====================================================================//
            // This is Splash WebHook
            if (trim($webHook['url']) == $webHookUrl) {
                return true;
            }
        }

        //====================================================================//
        // Splash WebHooks was NOT Found
        return false;
    }

    /**
     * Check & Update SendInBlue Api Account WebHooks.
     *
     * @return bool
     */
    public function updateWebHooks() : bool
    {
        //====================================================================//
        // Connector SelfTest
        if (!$this->selfTest()) {
            return false;
        }
        //====================================================================//
        // Generate WebHook Url
        $webHookUrl = $this->getRouteBuilder()->getMasterActionUrl($this);
        //====================================================================//
        // Create Object Class
        $webHookManager = new Objects\WebHook($this);
        $webHookManager->configure("webhook", $this->getWebserviceId(), $this->getConfiguration());
        //====================================================================//
        // Get List Of WebHooks for this List
        $webHooks = $webHookManager->objectsList();
        if (isset($webHooks["meta"])) {
            unset($webHooks["meta"]);
        }
        //====================================================================//
        // Filter & Clean List Of WebHooks
        $foundWebHook = false;
        foreach ($webHooks as $webHook) {
            //====================================================================//
            // This is Current Node WebHooks
            if (trim($webHook['url']) == $webHookUrl) {
                $foundWebHook = true;

                continue;
            }
            //====================================================================//
            // This is a Splash WebHooks
            if ($this->getRouteBuilder()->isSplashUrl($webHook['url'])) {
                $webHookManager->delete($webHook['id']);
            }
        }
        //====================================================================//
        // Splash WebHooks was Found
        if ($foundWebHook) {
            return true;
        }

        //====================================================================//
        // Add Splash WebHooks
        return (false !== $webHookManager->create($webHookUrl));
    }
}
