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

use ArrayObject;
use Psr\Log\LoggerInterface;
use Splash\Bundle\Interfaces\Connectors\PrimaryKeysInterface;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Bundle\Models\Connectors\GenericObjectMapperTrait;
use Splash\Bundle\Models\Connectors\GenericObjectPrimaryMapperTrait;
use Splash\Bundle\Models\Connectors\GenericWidgetMapperTrait;
use Splash\Bundle\Models\Connectors\RoutesBuilderAwareTrait;
use Splash\Bundle\Services\ConnectorRoutesBuilder;
use Splash\Connectors\Brevo\Actions;
use Splash\Connectors\Brevo\Form\EditFormType;
use Splash\Connectors\Brevo\Form\NewFormType;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;
use Splash\Connectors\Brevo\Objects;
use Splash\Core\SplashCore as Splash;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * SendInBlue REST API Connector for Splash
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class BrevoConnector extends AbstractConnector implements PrimaryKeysInterface
{
    use GenericObjectMapperTrait;
    use GenericObjectPrimaryMapperTrait;
    use GenericWidgetMapperTrait;
    use RoutesBuilderAwareTrait;

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
     * Class Constructor
     */
    public function __construct(
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
        return API::ping();
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
        // Perform Connect Test
        if (!API::connect()) {
            return false;
        }
        //====================================================================//
        // Get List of Available Lists
        if (!$this->fetchMailingLists()) {
            return false;
        }
        //====================================================================//
        // Get List of Available Members Properties
        if (!$this->fetchAttributesLists()) {
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
        $informations->servertype = "SendInBlue REST Api V3";
        $informations->serverurl = API::ENDPOINT;
        //====================================================================//
        // Module Information
        $informations->moduleauthor = SPLASH_AUTHOR;
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
        // Configure Rest API
        return API::configure(
            $config["ApiKey"],
            $config["ApiList"] ?? null
        );
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
        Splash::log()->err("There are No Files Reading for Mailchime Up To Now!");

        return null;
    }

    //====================================================================//
    // Profile Interfaces
    //====================================================================//

    /**
     * Get Connector Profile Information
     *
     * @return array
     */
    public function getProfile() : array
    {
        return array(
            'enabled' => true,                                  // is Connector Enabled
            'beta' => false,                                    // is this a Beta release
            'type' => self::TYPE_ACCOUNT,                       // Connector Type or Mode
            'name' => 'sendinblue',                             // Connector code (lowercase, no space allowed)
            'connector' => 'splash.connectors.sendinblue',      // Connector Symfony Service
            'title' => 'profile.card.title',                    // Public short name
            'label' => 'profile.card.label',                    // Public long name
            'domain' => 'SendInBlueBundle',                     // Translation domain for names
            'ico' => '/bundles/brevo/img/Brevo-Icon.png',       // Public Icon path
            'www' => 'www.SendInBlue.com',                      // Website Url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectedTemplate() : string
    {
        return "@Brevo/Profile/connected.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getOfflineTemplate() : string
    {
        return "@Brevo/Profile/offline.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getNewTemplate() : string
    {
        return "@Brevo/Profile/new.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getFormBuilderName() : string
    {
        return $this->getParameter("ApiListsIndex", false) ? EditFormType::class : NewFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterAction(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicActions() : array
    {
        return array(
            "index" => Actions\Master::class,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSecuredActions() : array
    {
        return array(
            "webhooks" => Actions\WebhooksUpdate::class,
        );
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

    //====================================================================//
    //  LOW LEVEL PRIVATE FUNCTIONS
    //====================================================================//

    /**
     * Get SendInBlue User Lists
     *
     * @return bool
     */
    private function fetchMailingLists(): bool
    {
        //====================================================================//
        // Get User Lists from Api
        $response = API::get('contacts/lists');
        if (is_null($response)) {
            return false;
        }
        if (!isset($response->lists)) {
            return false;
        }
        //====================================================================//
        // Parse Lists to Connector Settings
        $listIndex = array();
        foreach ($response->lists as $listDetails) {
            //====================================================================//
            // Add List Index
            $listIndex[$listDetails->id] = $listDetails->name;
        }
        //====================================================================//
        // Store in Connector Settings
        $this->setParameter("ApiListsIndex", $listIndex);
        $this->setParameter("ApiListsDetails", $response->lists);
        //====================================================================//
        // Update Connector Settings
        $this->updateConfiguration();

        return true;
    }

    /**
     * Get SendInBlue User Attributes Lists
     *
     * @return bool
     */
    private function fetchAttributesLists(): bool
    {
        //====================================================================//
        // Get User Lists from Api
        $response = API::get('contacts/attributes');
        if (is_null($response)) {
            return false;
        }
        // @codingStandardsIgnoreStart
        if (!isset($response->attributes)) {
            return false;
        }
        //====================================================================//
        // Store in Connector Settings
        $this->setParameter("ContactAttributes", $response->attributes);
        // @codingStandardsIgnoreEnd
        //====================================================================//
        // Update Connector Settings
        $this->updateConfiguration();

        return true;
    }
}
