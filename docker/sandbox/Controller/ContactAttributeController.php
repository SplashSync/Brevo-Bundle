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

use App\Entity\ContactAttribute;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Brevo API Sandbox - Contact Attributes endpoint.
 */
class ContactAttributeController extends AbstractController
{
    /**
     * Get all contact attribute definitions.
     */
    #[Route('/v3/contacts/attributes', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $attributes = $em->getRepository(ContactAttribute::class)->findAll();
        $result = array();
        foreach ($attributes as $attr) {
            $result[] = array(
                'name' => $attr->name,
                'category' => $attr->category,
                'type' => $attr->type,
                'enumeration' => $attr->enumeration,
            );
        }

        return new JsonResponse(array('attributes' => $result));
    }
}
