<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Setup Admin
        $admin = new User();
        // Suffix added since username must be strictly unique.
        $admin->setUsername('admin_rtimiranim80@gmail.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_WORKER']);
        $admin->setPasswordHash(
            $this->passwordHasher->hashPassword($admin, 'mohamed123')
        );
        $manager->persist($admin);

        // 2. Setup Worker
        $worker = new User();
        $worker->setUsername('rtimiranim80@gmail.com');
        $worker->setRoles(['ROLE_WORKER']);
        $worker->setPasswordHash(
            $this->passwordHasher->hashPassword($worker, 'rached123')
        );
        $manager->persist($worker);

        // 3. Optional: Add a few products so the worker form has options
        $product1 = new Product();
        $product1->setName('Baguette Tradition');
        $product1->setPrice(1.5);
        $product1->setCategory('Bread');
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Croissant au Beurre');
        $product2->setPrice(3.0);
        $product2->setCategory('Pastry');
        $manager->persist($product2);
        
        $product3 = new Product();
        $product3->setName('Pain au Chocolat');
        $product3->setPrice(3.5);
        $product3->setCategory('Pastry');
        $manager->persist($product3);

        $manager->flush();
    }
}
