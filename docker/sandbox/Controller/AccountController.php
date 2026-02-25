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

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Brevo API Sandbox - Get account information (singleton).
 */
#[AsController]
class AccountController extends AbstractController
{
    public function __invoke(EntityManagerInterface $em): JsonResponse
    {
        $account = $em->getRepository(Account::class)->findOneBy(array());
        if (!$account) {
            return new JsonResponse(array('code' => 'not_found', 'message' => 'Account not found'), 404);
        }

        return new JsonResponse(array(
            'companyName' => $account->companyName,
            'email' => $account->email,
            'address' => array(
                'street' => $account->street ?? '',
                'zipCode' => $account->zipCode ?? '',
                'city' => $account->city ?? '',
                'country' => $account->country ?? '',
            ),
        ));
    }
}
