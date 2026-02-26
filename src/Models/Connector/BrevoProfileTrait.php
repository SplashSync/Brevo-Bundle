<?php

namespace Splash\Connectors\Brevo\Models\Connector;

use Splash\Connectors\Brevo\Actions;
use Splash\Connectors\Brevo\Form\EditFormType;
use Splash\Connectors\Brevo\Form\NewFormType;

/**
 * Manage Brevo Connector Profile
 */
trait BrevoProfileTrait
{
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
            'domain' => 'BrevoBundle',                          // Translation domain for names
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

}