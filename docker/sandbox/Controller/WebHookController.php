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

use App\Entity\WebHook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Brevo API Sandbox - WebHook custom endpoints.
 *
 * Only list endpoint is handled here (wrapped format).
 * Single-item CRUD is handled natively by API Platform.
 */
class WebHookController extends AbstractController
{
    /**
     * List webhooks with optional type filter (wrapped format).
     */
    #[Route('/v3/webhooks', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $type = $request->query->get('type');
        $criteria = $type ? array('type' => $type) : array();
        $webhooks = $em->getRepository(WebHook::class)->findBy($criteria);

        $result = array();
        foreach ($webhooks as $webhook) {
            $result[] = array(
                'id' => $webhook->id,
                'description' => $webhook->description,
                'url' => $webhook->url,
                'type' => $webhook->type,
                'events' => $webhook->events,
            );
        }

        return new JsonResponse(array('webhooks' => $result));
    }
}
