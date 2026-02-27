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

namespace App\Controller\Contact;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - List all contacts (wrapped format).
 */
#[AsController]
class ListingController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 50);
        $offset = (int) $request->query->get('offset', 0);

        //====================================================================//
        // Count total contacts
        $total = (int) $em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Contact::class, 'c')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        //====================================================================//
        // Get paginated contacts
        $contacts = $em->getRepository(Contact::class)->findBy(
            array(),
            array('id' => 'ASC'),
            $limit,
            $offset
        );

        //====================================================================//
        // Format response
        $result = array();
        foreach ($contacts as $contact) {
            $result[] = array(
                'id' => $contact->id,
                'email' => $contact->email,
                'emailBlacklisted' => $contact->emailBlacklisted,
                'smsBlacklisted' => $contact->smsBlacklisted,
                'createdAt' => $contact->createdAt->format('Y-m-d\TH:i:s.000P'),
                'modifiedAt' => $contact->modifiedAt->format('Y-m-d\TH:i:s.000P'),
                'listIds' => $contact->getListIds(),
                'attributes' => $contact->attributes,
            );
        }

        return new JsonResponse(array('contacts' => $result, 'count' => $total));
    }
}
