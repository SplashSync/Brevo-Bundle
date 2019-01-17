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

namespace Splash\Connectors\SendInBlue\Models;

use Httpful\Exception\ConnectionErrorException;
use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;
use Splash\Core\SplashCore as Splash;
use stdClass;

/**
 * SendInBlue Specific Helper
 *
 * Support for Managing ApiKey, ApiRequests, Hashs, Etc...
 */
class SendInBlueHelper
{
    /**
     * @var string
     */
    const ENDPOINT = "https://api.sendinblue.com/v3/";
    
    /**
     * @var string
     */
    private static $apiList;
    
    /**
     * Get Current SendInBlue List
     *
     * @return string
     */
    public static function getList(): string
    {
        return self::$apiList;
    }
    
    /**
     * Configure SendInBlue REST API
     *
     * @param string $apiKey
     * @param string $apiList
     *
     * @return bool
     */
    public static function configure(string $apiKey, string $apiList = null): bool
    {
        //====================================================================//
        // Store Current List to Use
        self::$apiList = is_string($apiList) ? $apiList : "";
        //====================================================================//
        // Configure API Template Request
        $template = Request::init(null, Mime::JSON)
            ->addHeaders(array(
                'Content-Type'  => 'application/json',
                'api-key' => $apiKey,
            ))
            ->timeout(3)
            ;
        // Set it as a template
        Request::ini($template);

        return true;
    }
    
    /**
     * Ping SendInBlue API Url as Annonymous User
     *
     * @return bool
     */
    public static function ping(): bool
    {
        //====================================================================//
        // Perform Ping Test
        try {
            $response = Request::get(self::ENDPOINT."account")
                ->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return false;
        }
        
        if (($response->code >= 200) && ($response->code < 500)) {
            return true;
        }

        return false;
    }
    
    /**
     * Ping SendInBlue API Url with API Key (Logged User)
     *
     * @return bool
     */
    public static function connect(): bool
    {
        //====================================================================//
        // Perform Connect Test
        try {
            $response = Request::get(self::ENDPOINT."account")
                ->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return false;
        }
        //====================================================================//
        // Catch Errors inResponse
        self::catchErrors($response);
        //====================================================================//
        // Return Connect Result
        return (200 == $response->code);
    }
    
    /**
     * SendInBlue API GET Request
     *
     * @param string $path API REST Path
     * @param array  $body Request Data
     *
     * @return null|stdClass
     */
    public static function get(string $path, array $body = null): ?stdClass
    {
        //====================================================================//
        // Prepare Uri
        $uri = self::ENDPOINT.$path;
        if (!empty($body)) {
            $uri .= "?".http_build_query($body);
        }
        //====================================================================//
        // Perform Request
        try {
            $response = Request::get($uri)
                ->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return null;
        }
        //====================================================================//
        // Catch Errors inResponse
        return self::catchErrors($response) ? $response->body : null;
    }
    
    /**
     * SendInBlue API PUT Request
     *
     * @param string   $path API REST Path
     * @param stdClass $body Request Data
     *
     * @return null|bool
     */
    public static function put(string $path, stdClass $body): ?bool
    {
        //====================================================================//
        // Perform Request
        try {
            $response = Request::put(self::ENDPOINT.$path)
                ->body($body)
                ->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return null;
        }
        //====================================================================//
        // Catch Errors inResponse
        return self::catchErrors($response);
    }
    
    /**
     * SendInBlue API POST Request
     *
     * @param string   $path API REST Path
     * @param stdClass $body Request Data
     *
     * @return null|stdClass
     */
    public static function post(string $path, stdClass $body): ?stdClass
    {
        //====================================================================//
        // Perform Request
        try {
            $response = Request::post(self::ENDPOINT.$path)
                ->body($body)
                ->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return null;
        }
        //====================================================================//
        // Catch Errors inResponse
        return self::catchErrors($response) ? $response->body : null;
    }
    
    /**
     * SendInBlue API DELETE Request
     *
     * @param string $path API REST Path
     *
     * @return null|bool
     */
    public static function delete(string $path): ?bool
    {
        //====================================================================//
        // Perform Request
        try {
            $response = Request::delete(self::ENDPOINT.$path)->send();
        } catch (ConnectionErrorException $ex) {
            Splash::log()->err($ex->getMessage());

            return null;
        }
        //====================================================================//
        // Catch Errors in Response
        return self::catchErrors($response) ? true : false;
    }
    
    /**
     * Analyze SendInBlue Api Response & Push Errors to Splash Log
     *
     * @param Response $response
     *
     * @return bool TRUE is no Error
     */
    private static function catchErrors(Response $response) : bool
    {
        //====================================================================//
        // Check if SendInBlue Response has Errors
        if (!$response->hasErrors()) {
            return true;
        }
        //====================================================================//
        //  Debug Informations
        if (true == SPLASH_DEBUG) {
            Splash::log()->www("[SendInBlue] Full Response", $response);
        }
        if ($response->hasBody()) {
            //====================================================================//
            // Store SendInBlue Errors if present
            if (isset($response->body->code, $response->body->message)) {
                Splash::log()->err($response->body->code." => ".$response->body->message);
            }
        }

        return false;
    }
}