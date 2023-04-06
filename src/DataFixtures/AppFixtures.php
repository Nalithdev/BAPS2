<?php

namespace App\DataFixtures;

use App\Entity\Commerce;
use App\Entity\Feed;
use App\Entity\Product;
use App\Entity\Reservation;
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
        $user->setLoyaltyPoints(0);
		
		$user->setRoles(['ROLE_USER']);
		
		$manager->persist($user);
		
		$manager->flush();
		
		//Création d'un admin
		$admin = new User();
		$admin->setEmail('admin@antony.com');
		$admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
		$admin->setFirstname('Admin');
		$admin->setLastname('Antony');
		
		$admin->setRoles(['ROLE_ADMIN']);
		
		$manager->persist($admin);
		
		$manager->flush();
		
		//Création d'un commerçant
		
		$merchant = new User();
		$merchant->setEmail('philippe.lafont@gmail.com');
		$merchant->setPassword($this->passwordHasher->hashPassword($merchant, 'password'));
		$merchant->setFirstname('Philippe');
		$merchant->setLastname('Lafont');
		$merchant->setSiren('123456789');
		$merchant->setApproved(false);
		$merchant->setLoyaltyPoints(100);
		$merchant->setRoles(['ROLE_MERCHANT']);
		
		$manager->persist($merchant);
		
		$manager->flush();


        //Création d'un commerce


            $shop= new Commerce();
            $shop->setName($merchant->getFirstname() . ' ' . $merchant->getLastname());
            $shop->setDescription('description du commerce');

            $manager->persist($shop);
            $merchant->setCommerce($shop);


        $manager->flush();



        for ($i = 1; $i <= 100; $i++) {
            $message = new Feed();
            $message->setTitle('Titre ' . $i);
            $message->setDescription('Description ' . $i);
            $message->setUser($merchant);
            $message->setCDate(date('Y-m-d H:i:s'));
            $manager->persist($message);
        }
        $manager->flush();
        //Création d'un produits

        // 1 - Appel d'un shop

        $shopRepository = $manager->getRepository(Commerce::class);
        $shop = $shopRepository->findAll();


        // 2 - Création de 10 produits

        for($i = 1; $i <= 30; $i++){
            $product = new Product();
            $product->setName('Produit ' . $i);
            $product->setDescription('Description du produit ' . $i);
            $product->setPrice(10);
            $product->setStock(10);
            $product->setShop($shop[rand(0, count($shop) - 1)]);
            $manager->persist($product);
        }

        $manager->flush();

        $productRepository = $manager->getRepository(Product::class);
        $product = $productRepository->findAll();
        for ($i = 1; $i <= 20; $i++) {
            $reservation = new Reservation();
            $reservation->setUser($user);
            $reservation->setProduct($product[rand(0, count($product) - 1)]);
            $reservation->setQuantity(rand(1, 100));
            date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();
            $reservation->setStatus('in waiting');

            $reservation->setCDate($date);
            $manager->persist($reservation);
        }
        $manager->flush();

       /* $productRepository = $manager->getRepository(Product::class);
        $product = $productRepository->findAll();
            $reservation2 = new Reservation();
            $reservation2->setUser($user);
            $reservation2->setProduct($product[rand(0, count($product) - 1)]);
            $reservation2->setQuantity(rand(1, 100));
            $reservation2->setStatus('reserved');
            date_default_timezone_set('Europe/Paris');
            $date = new \DateTime();

            $reservation2->setCDate($date);
            $manager->persist($reservation2);

        $manager->flush();*/



//Créer des reservations
        //recuperer l'user avec le role merchant




    }

}
