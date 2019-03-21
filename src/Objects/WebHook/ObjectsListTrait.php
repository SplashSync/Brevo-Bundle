<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Connectors\SendInBlue\Objects\WebHook;

use Splash\Connectors\SendInBlue\Models\SendInBlueHelper as API;

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
    public function objectsList($filter = null, $params = null)
    {
        //====================================================================//
        // Get User Lists from Api
        $rawData = API::get('webhooks', array("type" => "marketing"));
        //====================================================================//
        // Request Failed
        if (null == $rawData) {
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
