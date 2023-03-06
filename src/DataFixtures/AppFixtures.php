<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AppFixtures extends Fixture
{
	
	public function __construct(private UserPasswordHasherInterface $passwordHasher){}
	
    public function load(ObjectManager $manager): void
    {
        //Créons un utilisateur
		$user = new User();
		$user->setEmail('francisbertrand@gmail.com');
		$user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
		$user->setFirstname('Francis');
		$user->setLastname('Bertrand');
		
		$user->setRoles(['ROLE_USER']);
		
		$manager->persist($user);
		
		$manager->flush();
		
		//Créons un admin
		$admin = new User();
		$admin->setEmail('admin@antony.com');
		$admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
		$admin->setFirstname('Admin');
		$admin->setLastname('Antony');
		
		$admin->setRoles(['ROLE_ADMIN']);
		
		$manager->persist($admin);
		
		$manager->flush();
		
		//Créons un commerçant
		
		$merchant = new User();
		$merchant->setEmail('philippe.lafont@gmail.com');
		$merchant->setPassword($this->passwordHasher->hashPassword($merchant, 'password'));
		$merchant->setFirstname('Philippe');
		$merchant->setLastname('Lafont');
		$merchant->setSiren('123456789');
		$merchant->setApproved(false);
		
		$merchant->setRoles(['ROLE_MERCHANT']);
		
		$manager->persist($merchant);
		
		$manager->flush();
		
    }
}
