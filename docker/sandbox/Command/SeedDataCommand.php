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

namespace App\Command;

use App\Entity\Account;
use App\Entity\ContactAttribute;
use App\Entity\ContactList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Seeds the sandbox database with default data for Brevo API testing.
 */
#[AsCommand(name: 'app:seed-data', description: 'Seed sandbox with default Brevo data')]
class SeedDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->seedAccount($output);
        $this->seedContactList($output);
        $this->seedContactAttributes($output);

        $this->em->flush();
        $output->writeln('<info>Seed data loaded successfully.</info>');

        return Command::SUCCESS;
    }

    private function seedAccount(OutputInterface $output): void
    {
        if ($this->em->getRepository(Account::class)->count(array()) > 0) {
            $output->writeln('Account already exists, skipping.');

            return;
        }

        $account = new Account();
        $account->companyName = 'SplashSync Test';
        $account->email = 'test@splashsync.com';
        $account->street = '1 rue de la Paix';
        $account->zipCode = '75000';
        $account->city = 'Paris';
        $account->country = 'France';

        $this->em->persist($account);
        $output->writeln('Account seeded.');
    }

    private function seedContactList(OutputInterface $output): void
    {
        if ($this->em->getRepository(ContactList::class)->count(array()) > 0) {
            $output->writeln('ContactList already exists, skipping.');

            return;
        }

        $lists = array(
            array('name' => 'Newsletter', 'totalSubscribers' => 0),
            array('name' => 'Clients', 'totalSubscribers' => 0),
            array('name' => 'Prospects', 'totalSubscribers' => 0),
            array('name' => 'VIP', 'totalSubscribers' => 0),
            array('name' => 'Partners', 'totalSubscribers' => 0),
        );

        foreach ($lists as $data) {
            $list = new ContactList();
            $list->name = $data['name'];
            $list->totalSubscribers = $data['totalSubscribers'];

            $this->em->persist($list);
        }

        $output->writeln(sprintf('%d ContactLists seeded.', count($lists)));
    }

    private function seedContactAttributes(OutputInterface $output): void
    {
        if ($this->em->getRepository(ContactAttribute::class)->count(array()) > 0) {
            $output->writeln('ContactAttributes already exist, skipping.');

            return;
        }

        $attrs = array(
            array('name' => 'NOM', 'category' => 'normal', 'type' => 'text'),
            array('name' => 'PRENOM', 'category' => 'normal', 'type' => 'text'),
            array('name' => 'SMS', 'category' => 'normal', 'type' => 'text'),
            array(
                'name' => 'CIVILITE',
                'category' => 'category',
                'type' => 'category',
                'enumeration' => array(
                    array('value' => "m", 'label' => 'Mr'),
                    array('value' => "f", 'label' => 'Mme'),
                    array('value' => "u", 'label' => 'Iel'),
                ),
            ),
            array('name' => 'DATE_NAISSANCE', 'category' => 'normal', 'type' => 'date'),
        );

        foreach ($attrs as $data) {
            $attr = new ContactAttribute();
            $attr->name = $data['name'];
            $attr->category = $data['category'];
            $attr->type = $data['type'];
            $attr->enumeration = $data['enumeration'] ?? array();

            $this->em->persist($attr);
        }

        $output->writeln('ContactAttributes seeded.');
    }
}
