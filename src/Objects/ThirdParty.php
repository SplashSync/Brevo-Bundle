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

namespace Splash\Connectors\Brevo\Objects;

use Splash\Bundle\Models\AbstractStandaloneObject;
use Splash\Connectors\Brevo\Connectors\BrevoConnector;
use Splash\Connectors\Brevo\Helpers\ContactIdHelper;
use Splash\Connectors\Brevo\Models\Api\Contact;
use Splash\Connectors\Brevo\Models\BrevoApiHelper as API;
use Splash\Core\Client\Splash;
use Splash\Core\Interfaces\Object\PrimaryKeysAwareInterface;
use Splash\Core\Models\Objects\IntelParserTrait;
use Splash\Core\Models\Objects\SimpleFieldsTrait;
use Splash\OpenApi\Action\JsonLd\ListAction;
use Splash\OpenApi\Action\Json\PutAction;
use Splash\OpenApi\Dictionary\ActionOptions;
use Splash\OpenApi\Models\Objects\AbstractRestAndMetadataObject;

/**
 * SendInBlue Implementation of ThirdParty
 */
class ThirdParty extends AbstractRestAndMetadataObject implements PrimaryKeysAwareInterface
{
    use ThirdParty\CRUDTrait;
    use ThirdParty\PrimaryTrait;
    use ThirdParty\ListsTrait;
    use ThirdParty\AttributesTrait;

    /**
     * @var Contact
     */
    protected object $object;

    /**
     * Class Constructor
     */
    public function __construct(
        protected readonly BrevoConnector $connector
    ) {
        parent::__construct(
            $visitor = $connector->getVisitor(Contact::class),
            $visitor->getMetadataAdapter(),
            Contact::class
        );
        //====================================================================//
        // Configure Search by Email
        $this->visitor->setListAction(ListAction::class, array(
            ActionOptions::PAGE_KEY => null,
            ActionOptions::OFFSET_KEY => "offset",
            ActionOptions::MEMBER_KEY => "contacts",
            ActionOptions::TOTAL_KEY => "count",
        ));
        //====================================================================//
        //  Load Translation File
        Splash::translator()->load('local');
    }
}
