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

namespace Splash\Connectors\SendInBlue\Objects\ThirdParty;

use DateTime;
use Splash\Core\SplashCore      as Splash;

/**
 * SendInBlue ThirdParty Meta Fields
 */
trait MetaTrait
{
    /**
    * Build Fields using FieldFactory
    */
    protected function buildMetaFields()
    {
        
        //====================================================================//
        // TRACEABILITY INFORMATIONS
        //====================================================================//
        
        //====================================================================//
        // Last Change Date
        $this->fieldsFactory()->create(SPL_T_DATETIME)
                ->Identifier("modifiedAt")
                ->Name("Last modification")
                ->Group("Meta")
                ->MicroData("http://schema.org/DataFeedItem", "dateModified")
                ->isListed()
                ->isReadOnly();
    }
    
    /**
     * Read requested Field
     *
     * @param        string $key       Input List Key
     * @param        string $fieldName Field Identifier / Name
     *
     * @return         none
     */
    protected function getMetaFields($key, $fieldName)
    {
        //====================================================================//
        // Does the Field Exists?
        if (!in_array($fieldName, ['modifiedAt'])) {
            return;
        }
        //====================================================================//
        // Insert in Response      
        $Date = new DateTime($this->object->$fieldName);
        $this->out[$fieldName] = $Date->format(SPL_T_DATETIMECAST);
        //====================================================================//
        // Clear Key Flag
        unset($this->in[$key]);
    }
}
