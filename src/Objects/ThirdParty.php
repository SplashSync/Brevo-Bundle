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

namespace Splash\Connectors\SendInBlue\Objects;

use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Connectors\SendInBlue\Services\SendInBlueConnector;
use Splash\Models\Objects\IntelParserTrait;
use Splash\Models\Objects\SimpleFieldsTrait;
use stdClass;

/**
 * SendInBlue Implementation of ThirdParty
 */
class ThirdParty extends AbstractStandaloneObject
{
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use ThirdParty\CRUDTrait;
    use ThirdParty\ObjectsListTrait;
    use ThirdParty\CoreTrait;
    use ThirdParty\AttributesTrait;
    use ThirdParty\MetaTrait;

    /**
     * {@inheritdoc}
     */
    protected static bool $disabled = false;

    /**
     * {@inheritdoc}
     */
    protected static string $name = "Customer";

    /**
     * {@inheritdoc}
     */
    protected static string $description = "SendInBlue Contact";

    /**
     * {@inheritdoc}
     */
    protected static string $ico = "fa fa-user";

    /**
     * @phpstan-var stdClass
     */
    protected object $object;

    /**
     * @var SendInBlueConnector
     */
    protected SendInBlueConnector $connector;

    /**
     * Class Constructor
     *
     * @param SendInBlueConnector $parentConnector
     */
    public function __construct(SendInBlueConnector $parentConnector)
    {
        $this->connector = $parentConnector;
    }

    /**
     * Encode Contact Email to Splash Id String
     *
     * @param string $email
     *
     * @return string
     */
    public static function encodeContactId(string $email)
    {
        return base64_encode(strtolower($email));
    }

    /**
     * Decode Contact Email from Splash Id String
     *
     * @param string $contactId
     *
     * @return string
     */
    protected static function decodeContactId(string $contactId)
    {
        return (string) base64_decode($contactId, true);
    }
}
