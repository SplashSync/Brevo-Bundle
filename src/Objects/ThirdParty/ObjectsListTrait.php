<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Connectors\SendInBlue\Objects\ThirdParty;

use DateTime;
use Splash\Connectors\SendInBlue\Models\SendInBlueHelper as API;

/**
 * SendInBlue Users Objects List Functions
 */
trait ObjectsListTrait
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function objectsList(string $filter = null, array $params = array()): array
    {
        //====================================================================//
        // Prepare Parameters
        $body = array();
        if (isset($params["max"], $params["offset"])) {
            $body['limit'] = $params["max"];
            $body['offset'] = $params["offset"];
        }
        //====================================================================//
        // Get User Lists from Api
        $rawData = API::get('contacts/lists/'.API::getList().'/contacts', $body);
        //====================================================================//
        // Request Failed
        if ((null == $rawData) || !isset($rawData->contacts)) {
            return array( 'meta' => array('current' => 0, 'total' => 0));
        }
        //====================================================================//
        // Compute Totals
        $response = array(
            'meta' => array('current' => count($rawData->contacts), 'total' => $rawData->count),
        );
        //====================================================================//
        // Parse Data in response
        foreach ($rawData->contacts as $member) {
            $response[] = array(
                'id' => self::encodeContactId($member->email),
                'email' => $member->email,
                'emailBlacklisted' => $member->emailBlacklisted,
                'smsBlacklisted' => $member->smsBlacklisted,
                'modifiedAt' => (new DateTime($member->modifiedAt))->format(SPL_T_DATETIMECAST),
            );
        }

        return $response;
    }
}
