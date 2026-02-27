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

use App\Entity\ContactList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - Get all contact lists (wrapped format).
 */
#[AsController]
class ListingController extends AbstractController
{
    public function __invoke(EntityManagerInterface $em): JsonResponse
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
}