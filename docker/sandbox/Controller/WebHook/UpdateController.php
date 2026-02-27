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

namespace App\Controller\WebHook;

use App\Entity\WebHook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - Update a webhook by ID.
 *
 * Custom controller to replicate Brevo partial update behavior.
 */
#[AsController]
class UpdateController extends AbstractController
{
    public function __invoke(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $webhook = $em->getRepository(WebHook::class)->find($id);
        if (!$webhook) {
            return new JsonResponse(
                array('code' => 'document_not_found', 'message' => 'Webhook does not exist'),
                404
            );
        }

        $data = json_decode($request->getContent(), true) ?: array();

        //====================================================================//
        // Update fields
        if (array_key_exists('url', $data)) {
            $webhook->url = (string) $data['url'];
        }
        if (array_key_exists('description', $data)) {
            $webhook->description = $data['description'];
        }
        if (array_key_exists('type', $data)) {
            $webhook->type = $data['type'];
        }
        if (array_key_exists('events', $data)) {
            $webhook->events = (array) $data['events'];
        }

        $em->flush();

        return new Response(null, 204);
    }
}
