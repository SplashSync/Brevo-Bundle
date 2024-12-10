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

namespace Splash\Connectors\Brevo\Objects\WebHook;

use Splash\Client\Splash;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;

/**
 * SendInBlue WebHook Objects List Functions
 */
trait ObjectsListTrait
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function objectsList(string$filter = null, array $params = array()): array
    {
        //====================================================================//
        // Get User Lists from Api
        $rawData = API::get('webhooks', array("type" => "marketing"));
        //====================================================================//
        // Request Failed
        if (null == $rawData) {
            Splash::log()->cleanLog();

            return array( 'meta' => array('current' => 0, 'total' => 0));
        }
        //====================================================================//
        // Compute Totals
        $response = array(
            'meta' => array('current' => count($rawData->webhooks), 'total' => count($rawData->webhooks)),
        );
        //====================================================================//
        // Parse Data in response
        foreach ($rawData->webhooks as $webhook) {
            $response[] = array(
                'id' => $webhook->id,
                'description' => $webhook->description,
                'url' => $webhook->url,
            );
        }

        return $response;
    }
}
