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

namespace App\Controller\ContactList;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - Get contacts belonging to a specific list.
 */
#[AsController]
class ContactsController extends AbstractController
{
    public function __invoke(int $listId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 50);
        $offset = (int) $request->query->get('offset', 0);

        //====================================================================//
        // Get contacts that have this listId in their listIds JSON array
        $qb = $em->createQueryBuilder();
        $qb->select('c')
            ->from(Contact::class, 'c')
            ->where("c.listIds LIKE :listId")
            ->setParameter('listId', '%'.$listId.'%')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
        ;

        $contacts = $qb->getQuery()->getResult();

        //====================================================================//
        // Count total
        $qbCount = $em->createQueryBuilder();
        $qbCount->select('COUNT(c.id)')
            ->from(Contact::class, 'c')
            ->where("c.listIds LIKE :listId")
            ->setParameter('listId', '%'.$listId.'%')
        ;
        $total = (int) $qbCount->getQuery()->getSingleScalarResult();

        //====================================================================//
        // Format response
        $result = array();
        foreach ($contacts as $contact) {
            $result[] = array(
                'id' => $contact->id,
                'email' => $contact->email,
                'emailBlacklisted' => $contact->emailBlacklisted,
                'smsBlacklisted' => $contact->smsBlacklisted,
                'modifiedAt' => $contact->modifiedAt->format('Y-m-d\TH:i:s.000P'),
            );
        }

        return new JsonResponse(array('contacts' => $result, 'count' => $total));
    }
}
