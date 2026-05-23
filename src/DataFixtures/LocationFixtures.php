<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture
{
    public const LOCATIONS = [
        'Paris',
        'Lyon',
        'Marseille',
        'Toulouse',
        'Nice',
        'Nantes',
        'Strasbourg',
        'Bordeaux',
        'Lille',
        'Rennes',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::LOCATIONS as $name) {
            $location = new Location();
            $location->setName($name);
            $manager->persist($location);
            $this->addReference('location_' . strtolower(str_replace(' ', '_', $name)), $location);
        }

        $manager->flush();
    }
}
