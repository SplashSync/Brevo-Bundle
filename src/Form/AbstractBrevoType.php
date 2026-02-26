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

namespace Splash\Connectors\Brevo\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Base Form Type for SendInBlue Connectors Servers
 */
abstract class AbstractBrevoType extends AbstractType
{
    /**
     * Translation Domain for this Connector
     */
    const string DOMAIN = "BrevoBundle";

    /**
     * Add Api Key Field to FormBuilder
     */
    public function addApiKeyField(FormBuilderInterface $builder): static
    {
        $builder
            //==============================================================================
            // SendInBlue Api Key Option Authentification
            ->add('ApiKey', TextType::class, array(
                'label' => "var.apikey.label",
                'help' => "var.apikey.desc",
                'required' => true,
                'translation_domain' => self::DOMAIN,
            ))
        ;

        return $this;
    }

    /**
     * Add List Selector Field to FormBuilder
     */
    public function addApiListField(FormBuilderInterface $builder, array $options): static
    {
        //==============================================================================
        // Check SendInBlue Lists are Available
        if (empty($options["data"]["ApiListsIndex"])) {
            return $this;
        }

        $builder
            //==============================================================================
            // SendInBlue List Option Selector
            ->add('ApiList', ChoiceType::class, array(
                'label' => "var.list.label",
                'help' => "var.list.desc",
                'required' => true,
                'translation_domain' => self::DOMAIN,
                'choice_translation_domain' => false,
                'choices' => array_flip($options["data"]["ApiListsIndex"]),
            ))
        ;

        return $this;
    }
}
