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
    private const STATUSES = ['Open', 'Assigned', 'In Progress', 'Completed', 'Cancelled'];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Fetch clients and categories
        $clients = $manager->getRepository(Client::class)->findAll();
        $categories = $manager->getRepository(\App\Entity\Category::class)->findAll();

        if (empty($clients) || empty($categories)) {
            return; // Skip if no clients or categories
        }

        $jobTitles = [
            'Plumbing repair needed',
            'Electrical socket installation',
            'Garden maintenance required',
            'Bathroom tiles repair',
            'Kitchen cabinet painting',
            'Door lock replacement',
            'Window cleaning service',
            'Wall painting needed',
            'AC maintenance and cleaning',
            'Furniture assembly',
        ];

        $descriptions = [
            'Need someone to fix a leaking pipe under the sink',
            'Two sockets in the kitchen need replacement - they are sparking',
            'Need someone to trim hedges and mow the lawn',
            'Bathroom tiles need repair and grouting',
            'Kitchen cabinets need a fresh coat of paint',
            'Front door lock is broken and needs replacement',
            'Windows need thorough cleaning inside and out',
            'Living room walls need fresh paint',
            'Air conditioning unit needs maintenance and cleaning',
            'IKEA furniture needs assembly and installation',
        ];

        for ($i = 0; $i < 10; $i++) {
            $command = new Command();
            $command->setTitle($jobTitles[$i % count($jobTitles)]);
            $command->setDescription($descriptions[$i % count($descriptions)]);
            $command->setPrice((string) $faker->randomFloat(2, 30, 500));
            $command->setStatus('Open'); // Only show Open jobs to providers
            $command->setCreatedAt(new \DateTimeImmutable($faker->dateTime()->format('Y-m-d H:i:s')));
            $command->setClient($clients[$faker->numberBetween(0, count($clients) - 1)]);
            $command->setCategory($categories[$faker->numberBetween(0, count($categories) - 1)]);

            $manager->persist($command);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ClientFixtures::class, ProviderFixtures::class];
    }
}
