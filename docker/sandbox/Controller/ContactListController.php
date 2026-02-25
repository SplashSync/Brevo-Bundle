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

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\ContactList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Brevo API Sandbox - Contact Lists endpoints.
 */
class ContactListController extends AbstractController
{
    /**
     * Get all contact lists.
     */
    #[Route('/v3/contacts/lists', methods: ['GET'])]
    public function lists(EntityManagerInterface $em): JsonResponse
    {
        $lists = $em->getRepository(ContactList::class)->findAll();
        $result = array();
        foreach ($lists as $list) {
            $result[] = array(
                'id' => $list->id,
                'name' => $list->name,
                'totalSubscribers' => $list->totalSubscribers,
            );
        }

        return new JsonResponse(array('lists' => $result, 'count' => count($result)));
    }

    /**
     * Get contacts belonging to a specific list.
     */
    #[Route('/v3/contacts/lists/{listId}/contacts', methods: ['GET'])]
    public function listContacts(int $listId, Request $request, EntityManagerInterface $em): JsonResponse
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
