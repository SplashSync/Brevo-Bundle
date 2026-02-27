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
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - List webhooks (wrapped format).
 */
#[AsController]
class ListingController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
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
