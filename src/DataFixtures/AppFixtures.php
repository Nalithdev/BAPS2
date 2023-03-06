<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Trader;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher

    )
    {


    }

    public function load(ObjectManager $manager,): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@example.com');
            $user->setFirstName('FIRSTNAME' . $i);
            $user->setLastName('LASTNAME');
            $user->setPassword($this->passwordHasher->hashPassword($user,'password'));
            $manager->persist($user);

            $trader = new Trader();
            $trader->setEmail('trader' . $i . '@example.com');
            $trader->setName('FIRSTNAME' . $i);
            $trader->setPassword($this->passwordHasher->hashPassword($trader,'password'));
            $manager->persist($trader);

        }


        $admin = new Admin();
        $admin->setEmail('Antony@gmail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin,'password'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);


        $manager->flush();
    }
}
