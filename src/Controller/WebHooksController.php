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

namespace Splash\Connectors\SendInBlue\Controller;

use Psr\Log\LoggerInterface;
use Splash\Bundle\Models\AbstractConnector;
use Splash\Connectors\SendInBlue\Objects\ThirdParty;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Splash SendInBlue WebHooks Actions Controller
 */
class WebHooksController extends Controller
{
    /**
     * @var string
     */
    const USER = "SendInBlue API";

    /**
     * @var string
     */
    const COMMENT = 'Contact has been Updated';

    //====================================================================//
    //  SendInBlue WEBHOOKS MANAGEMENT
    //====================================================================//

    /**
     * Execute WebHook Actions for A SendInBlue Connector
     *
     * @param LoggerInterface   $logger
     * @param Request           $request
     * @param AbstractConnector $connector
     *
     * @return JsonResponse
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function indexAction(LoggerInterface $logger, Request $request, AbstractConnector $connector)
    {
        //====================================================================//
        // For SendInBlue Ping Test
        if ("example@example.com" == $request->get('email')) {
            $logger->notice(__CLASS__.'::'.__FUNCTION__.' SendInBlue Ping.', $request->attributes->all());

            return $this->prepareResponse(200);
        }

        //====================================================================//
        // Read, Validate & Extract Request Parameters
        $eventData = $this->extractData($request);

        //====================================================================//
        // Log SendInBlue Request
        $logger->error(__CLASS__.'::'.__FUNCTION__.' SendInBlue WebHook Received ', $eventData);

        //==============================================================================
        // Commit Changes
        $this->executeCommits($connector, $eventData);

        return $this->prepareResponse(200);
    }

    /**
     * Execute Changes Commits
     *
     * @param AbstractConnector $connector
     * @param array             $eventData
     */
    private function executeCommits(AbstractConnector $connector, $eventData) : void
    {
        //==============================================================================
        // Check Infos are Available
        if (empty($eventData['email']) || empty($eventData['event'])) {
            return;
        }
        //==============================================================================
        // Check is in Selected List
        // TODO

        //==============================================================================
        // Commit Multiple Changes to Splash
        if (is_array($eventData['email'])) {
            foreach ($eventData['email'] as $eventEmail) {
                $email = ThirdParty::encodeContactId($eventEmail);
                $connector->commit('ThirdParty', $email, SPL_A_UPDATE, self::USER, self::COMMENT);
            }

            return;
        }
        //==============================================================================
        // Commit Single Changes to Splash
        $email = ThirdParty::encodeContactId($eventData['email']);
        $connector->commit('ThirdParty', $email, SPL_A_UPDATE, self::USER, self::COMMENT);
    }

    /**
     * Extract Data from Resquest
     *
     * @param Request $request
     *
     * @throws BadRequestHttpException
     *
     * @return array
     */
    private function extractData(Request $request): array
    {
        //==============================================================================
        // Safety Check => Data are here
        if (!$request->isMethod('POST')) {
            throw new BadRequestHttpException('Malformatted or missing data');
        }
        //==============================================================================
        // Decode Received Data
        $requestData = $request->request->all();
        //==============================================================================
        // Safety Check => Data are here
        if (empty($requestData) || !isset($requestData['event']) || !isset($requestData['email'])) {
            throw new BadRequestHttpException('Malformatted or missing data');
        }
        //==============================================================================
        // Return Request Data
        return $requestData;
    }

    /**
     * Preapare REST Json Response
     *
     * @param int $status
     *
     * @return JsonResponse
     */
    private function prepareResponse(int $status) :JsonResponse
    {
        return new JsonResponse(array('success' => true), $status);
    }
}
