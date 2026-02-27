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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - Update a contact by email.
 *
 * Custom controller to replicate Brevo merge behavior for listIds/unlinkListIds.
 */
#[AsController]
class UpdateController extends AbstractController
{
    public function __invoke(string $email, Request $request, EntityManagerInterface $em): Response
    {
        $contact = $em->getRepository(Contact::class)->findOneBy(array('email' => $email));
        if (!$contact) {
            return new JsonResponse(
                array('code' => 'document_not_found', 'message' => 'Contact does not exist'),
                404
            );
        }

        $data = json_decode($request->getContent(), true) ?: array();

        //====================================================================//
        // Update scalar fields
        if (array_key_exists('emailBlacklisted', $data)) {
            $contact->emailBlacklisted = (bool) $data['emailBlacklisted'];
        }
        if (array_key_exists('smsBlacklisted', $data)) {
            $contact->smsBlacklisted = (bool) $data['smsBlacklisted'];
        }
        if (array_key_exists('attributes', $data)) {
            $contact->attributes = (array) $data['attributes'];
        }

        //====================================================================//
        // Handle list changes (Brevo merge/unlink behavior)
        if (array_key_exists('listIds', $data)) {
            $contact->setListIds((array) $data['listIds']);
        }
        if (array_key_exists('unlinkListIds', $data)) {
            $contact->setUnlinkListIds((array) $data['unlinkListIds']);
        }

        $em->flush();

        return new Response(null, 204);
    }
}
