<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Fetch all users and filter by role programmatically
        $allUsers = $manager->getRepository(User::class)->findAll();
        $clientUsers = array_filter($allUsers, function(User $user) {
            return in_array('ROLE_CLIENT', $user->getRoles());
        });
        $clientUsers = array_slice($clientUsers, 0, 10);

        for ($i = 0; $i < count($clientUsers); $i++) {
            $client = new Client();
            $client->setUserAccount($clientUsers[$i]);

            $manager->persist($client);
            $this->addReference('client_' . $i, $client);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
