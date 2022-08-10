<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadClient(ObjectManager $manager): void
    {
        $admin = new Client();

        $this->addReference('admin', $admin);

        $admin->setName('Bill');
        $admin->setEmail('bile@mo.fr');

        $adminPassword = $this->passwordHasher->hashPassword($admin,'pomme');

        $admin->setPassword($adminPassword);
        $admin->setRoles(['ROLE_USER']);

        $manager->persist($admin);

        $faker = Factory::create();
        for ($i=1; $i <= 10; $i++) { 
            $client = new Client();
            $client->setName($faker->name())
                ->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setRoles(['ROLE_USER']);
            $manager->persist($client);
        }

        $manager->flush();
    }

    public function loadProduct(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i=1; $i <= 10; $i++) { 
            $product = new Product();
            $product->setBrand($faker->randomElement(['Samsung', 'Apple', 'Nokia', 'Huawei', 'Blackberry']))
                ->setName($faker->word())
                ->setModel($faker->word())
                ->setColor($faker->safeColorName())
                ->setPrice($faker->randomFloat())
                ->setDescription($faker->paragraph());
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i=1; $i <= 10; $i++) { 
            $user = new User();
            $user->setName($faker->name())
                ->setEmail($faker->email())
                ->setClient($this->getReference('admin'));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadClient($manager);
        $this->loadProduct($manager);
        $this->loadUser($manager);
    }
}
