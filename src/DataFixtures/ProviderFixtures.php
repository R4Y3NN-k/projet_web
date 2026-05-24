<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Provider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProviderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Fetch all users and filter by role programmatically
        $allUsers = $manager->getRepository(User::class)->findAll();
        $providerUsers = array_filter($allUsers, function(User $user) {
            return in_array('ROLE_PROVIDER', $user->getRoles());
        });
        $providerUsers = array_slice($providerUsers, 0, 10);
        
        $categories = $manager->getRepository(Category::class)->findAll();

        for ($i = 0; $i < count($providerUsers); $i++) {
            $provider = new Provider();
            $provider->setUserAccount($providerUsers[$i]);
            $provider->setYearsOfExperience($faker->numberBetween(1, 30));
            $provider->setCategory($categories[$i % count($categories)]);

            $manager->persist($provider);
            $this->addReference('provider_' . $i, $provider);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, CategoryFixtures::class];
    }
}
