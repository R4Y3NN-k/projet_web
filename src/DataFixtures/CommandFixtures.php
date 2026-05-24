<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommandFixtures extends Fixture implements DependentFixtureInterface
{
    private const STATUSES = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Fetch clients and providers
        $clients = $manager->getRepository(Client::class)->findAll();
        $providers = $manager->getRepository(Provider::class)->findAll();

        if (empty($clients)) {
            return; // Skip if no clients
        }

        for ($i = 0; $i < 20; $i++) {
            $command = new Command();
            $command->setTitle($faker->words(3, true));
            $command->setDescription($faker->paragraph());
            $command->setPrice((string) $faker->randomFloat(2, 50, 5000));
            $command->setStatus($faker->randomElement(self::STATUSES));
            $command->setCreatedAt(new \DateTimeImmutable($faker->dateTime()->format('Y-m-d H:i:s')));
            $command->setClient($clients[$faker->numberBetween(0, count($clients) - 1)]);
            
            // Randomly assign a provider
            if ($faker->boolean(70) && !empty($providers)) {
                $command->setProvider($providers[$faker->numberBetween(0, count($providers) - 1)]);
            }

            $manager->persist($command);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ClientFixtures::class, ProviderFixtures::class];
    }
}
