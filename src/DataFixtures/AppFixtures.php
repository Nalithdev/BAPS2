<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Ask;
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



        }







        for ($i = 1; $i <= 50; $i++) {


            $ask = new Ask();
            $ask->setEmail('traders' . $i . '@example.com');
            $ask->setName('FIRSTNAME' . $i);
            $ask->setPassword($this->passwordHasher->hashPassword($ask,'password'));
            $ask->setSiren('123456789');
            $manager->persist($ask);

        }
        $manager->flush();
        $admin = new Admin();
        $admin->setEmail('Antony@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin,'password'));
        $manager->persist($admin);
    }

    
}
