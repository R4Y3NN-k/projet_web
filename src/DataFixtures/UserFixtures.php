<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // Fetch all locations
        $locations = $manager->getRepository(Location::class)->findAll();
        $providerUsers = [];
        $clientUsers = [];
        
        // Create provider users
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser(
                $faker->firstName(),
                $faker->lastName(),
                $faker->email(),
                'password',
                ['ROLE_PROVIDER']
            );
            $user->setDateOfBirth($faker->dateTimeBetween('-60 years', '-18 years'));
            $user->setFullAdress($faker->address());
            $user->setLocation($locations[$i % count($locations)]);
            
            $manager->persist($user);
            $providerUsers[] = $user;
        }

        // Create client users
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser(
                $faker->firstName(),
                $faker->lastName(),
                $faker->email(),
                'password',
                ['ROLE_CLIENT']
            );
            $user->setDateOfBirth($faker->dateTimeBetween('-70 years', '-18 years'));
            $user->setFullAdress($faker->address());
            $user->setLocation($locations[$i % count($locations)]);
            
            $manager->persist($user);
            $clientUsers[] = $user;
        }

        $manager->flush();
        
        // Store references after flush so IDs are set
        foreach ($providerUsers as $i => $user) {
            $this->addReference('user_provider_' . $i, $user);
        }
        foreach ($clientUsers as $i => $user) {
            $this->addReference('user_client_' . $i, $user);
        }
    }

    private function createUser(string $firstName, string $lastName, string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setRoles($roles);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function getDependencies(): array
    {
        return [LocationFixtures::class];
    }
}
