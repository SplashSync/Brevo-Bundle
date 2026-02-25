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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Brevo API Sandbox - Contact custom endpoints.
 *
 * Only POST is handled here (for duplicate_parameter error format).
 * GET/PUT/DELETE are handled natively by API Platform.
 */
class ContactController extends AbstractController
{
    /**
     * Create a new contact (custom for duplicate handling).
     */
    #[Route('/v3/contacts', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: array();

        if (empty($data['email'])) {
            return new JsonResponse(
                array('code' => 'missing_parameter', 'message' => 'email is missing'),
                400
            );
        }

        //====================================================================//
        // Check for duplicate
        $existing = $em->getRepository(Contact::class)->findOneBy(array('email' => $data['email']));
        if ($existing) {
            return new JsonResponse(
                array('code' => 'duplicate_parameter', 'message' => 'Contact already exist'),
                400
            );
        }

        $contact = new Contact();
        $contact->email = $data['email'];
        $contact->emailBlacklisted = $data['emailBlacklisted'] ?? false;
        $contact->smsBlacklisted = $data['smsBlacklisted'] ?? false;
        $contact->attributes = $data['attributes'] ?? array();
        $contact->listIds = $data['listIds'] ?? array();

        $em->persist($contact);
        $em->flush();

        return new JsonResponse(array('id' => $contact->id), 201);
    }
}
