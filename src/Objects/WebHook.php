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
 * SendInBlue Implementation of WebHooks
 */
class WebHook extends AbstractStandaloneObject
{
    use IntelParserTrait;
    use SimpleFieldsTrait;
    use WebHook\CRUDTrait;
    use WebHook\CoreTrait;
    use WebHook\ObjectsListTrait;

    /**
     * {@inheritdoc}
     */
    protected static bool $disabled = true;

    /**
     * {@inheritdoc}
     */
    protected static string $name = "WebHook";

    /**
     * {@inheritdoc}
     */
    protected static string $description = "SendInBlue WebHook";

    /**
     * {@inheritdoc}
     */
    protected static string $ico = "fa fa-cogs";

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
}
