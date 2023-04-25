<?php

namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\Feed;
use App\Entity\Product;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CommerceRepository;
use App\Repository\FeedRepository;
use App\Repository\ProductRepository;
use App\Repository\ReservationRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\TokenAuthenticator;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class SecuredRouteController extends AbstractController
{
    public User $user;

    /**
     * @throws Exception
     */
    public function __construct(TokenAuthenticator $tokenAuthenticator, RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        $user = $tokenAuthenticator->getUser($request);

        if (!$user) throw new UnauthorizedHttpException("Bearer", 'vous devez être connecté');
        return $this->user = $user;
    }

    // TOUTES LES ROUTES CI DESSOUS NÉCESSITERONT L'AJOUT D'UN TOKEN UTILISATEUR DANS LE HEADER DE LA REQUETE


    #[Route('/message', name: 'app_SFeed', methods: ['POST'])]
    public function SendFeed(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $feed = $request->toArray();
        $title = $feed['title'];
        $description = $feed['description'];
        $id = $feed['id'];

        if (!$title || !$description) {
            return $this->json(['success' => false, 'message' => 'Veuillez remplir tous les champs']);
        }
        $dates = date('Y-m-d H:i:s');;
        $feed = new Feed();
        $feed->setTitle($title);
        $feed->setDescription($description);
        $feed->setUser($id);
        $feed->setCDate(strval($dates));

        $managerRegistry->getManager()->persist($feed);
        $managerRegistry->getManager()->flush();


        return $this->json(['success' => true, 'message' => 'Feed envoyer']);
    }


    #[Route('/message', name: 'app_Feed', methods: ['GET'])]
    public function Feed(FeedRepository $feedRepository, UserRepository $userRepository, Request $request): JsonResponse
    {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');
        $criteria = new Criteria();
        $criteria->orderBy(['id' => 'DESC']);
        $criteria->setMaxResults($limit ?? 50);
        $criteria->setFirstResult($offset ?? 0);
        $feed = $feedRepository->matching($criteria);

        $data = [];
        foreach ($feed as $f) {
            $user = $userRepository->findOneBy(['id' => $f->getUser()]);
            $data[] = [
                'id' => $f->getId(),
                'url' => '/api/message/' . $f->getId(),
            ];
        }

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/message/{id}', name: 'app_Feed_id', methods: ['GET'])]
    public function FeedId(FeedRepository $feedRepository, UserRepository $userRepository, $id, CommerceRepository $commerceRepository): JsonResponse
    {
        $feed = $feedRepository->findOneBy(['id' => $id]);
        $user = $userRepository->findOneBy(['id' => $feed->getUser()]);
        $shop = $commerceRepository->findOneBy(['id' => $user->getCommerce()]);

        $data = [
            'id' => $feed->getId(),
            'title' => $feed->getTitle(),
            'description' => $feed->getDescription(),
            'shop' => [
                'id' => $shop->getId(),
                'name' => $shop->getName(),
                'url' => '/api/shop/' . $shop->getId(),
            ],
            'date' => $feed->getCDate(),
        ];

        return $this->json(['success' => true, 'data' => $data]);
    }

    #[Route('/shop', name: 'app_DFeed', methods: ['POST'])]
    public function CreateShop(Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $user = $this->user;
        if ($user->getRoles()[0] == 'ROLE_MERCHANT') {
            $merchant = $request->toArray();

            $shop = new Commerce();
            $shop->setName($merchant['name']);
            $shop->setDescription($merchant['description']);
            $shop->setAdresse($merchant['address']);
            $managerRegistry->getManager()->persist($shop);
            $user->setCommerce($shop);
            $managerRegistry->getManager()->flush();

            return $this->json(['success' => true, 'message' => 'Votre page Commerce a bien été créer']);
        }
        return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
    }


    #[Route('/product', name: 'app_product', methods: ['POST'])]
    public function product(ManagerRegistry $managerRegistry, Request $request, CommerceRepository $commerceRepository): Response
    {
        $user = $this->user;
        if ($user->getRoles()[0] == 'ROLE_MERCHANT') {

            $commerce = $user->getCommerce();
            $shop = $commerceRepository->findOneBy(['id' => $commerce]);
            $product = $request->toArray();
            $product = new Product();
            $product->setName($product['name']);
            $product->setDescription($product['description']);
            $product->setPrice($product['price']);
            $product->setStock($product['stock']);
            $product->setShop($shop);
            $managerRegistry->getManager()->persist($product);
            $managerRegistry->getManager()->flush();

            return $this->json(['success' => true, 'message' => 'Produit envoyer', 'produit' => $product]);
        }
        return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);


    }
    #[Route('/product/{id}/modify', name: 'app_product', methods: ['POST'])]
    public function Mproduct(ManagerRegistry $managerRegistry, Request $request, ProductRepository $productRepository): Response
    {
        $user = $this->user;
        if ($user->getRoles()[0] == 'ROLE_MERCHANT') {

            $commerce = $user->getCommerce();
            $products = $productRepository->findOneBy(['id' => $commerce]);
            $product = $request->toArray();
            $products->setStock($product['stock']);

            $managerRegistry->getManager()->persist($products);
            $managerRegistry->getManager()->flush();

            return $this->json(['success' => true, 'message' => 'Produit modifier',]);
        }
        return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);


    }

    #[Route('/shop/{id}', name: 'app_product_id', methods: ['GET'])]
    public function show(ProductRepository $productRepository, CommerceRepository $commerceRepository, $id): Response
    {
        $shop = $commerceRepository->findOneBy(['id' => $id]);
        if ($shop == null) {
            return $this->json(['success' => false, 'message' => 'Ce commerce n\'existe pas']);
        }


        $product = $productRepository->findBy(['shop' => $shop]);
        $Tshop = array();
        $Tmessage = array();
        $Tshoop = array();

        $product_list = array();

        foreach ($product as $p) {
            $product_list[] = [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'description' => $p->getDescription(),
                'price' => $p->getPrice(),
                'stock' => $p->getStock(),
            ];
        }


        $Tshop = [
            'id' => $shop->getId(),
            'name' => $shop->getName(),
            'description' => $shop->getDescription(),
            'product' => $product_list,
            'adresse' => $shop->getAdresse(),
        ];


        return $this->json(['success' => true, 'message' => 'Envoie du commerce et de leur produit au client', 'shop' => $Tshop]);
    }

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function user(UserRepository $userRepository, $id): Response

    {
        $session = $this->user;

        $users = $userRepository->findOneBy(['id' => $id]);

        if ($session->getRoles()[0] == 'ROLE_ADMIN' || $session->getId() == $users->getId()) {
            $data = [
                'id' => $users->getId(),
                'firstname' => $users->getFirstname(),
                'lastname' => $users->getLastname(),
                'email' => $users->getEmail(),
                'role' => $users->getRoles()[0],
            ];
            return $this->json(['success' => true, 'user' => $data]);
        } else {
            return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
        }

    }

    #[Route('/reserved/', name: 'reserved_post', methods: ['POST'])]
    public function Reserved(UserRepository $userRepository, Request $request, ManagerRegistry $managerRegistry): Response

    {
        $session = $this->user;
        $reservation = new Reservation();
        $reservation->setUser($session);
        $reservation->setProduct($request->request->get('product'));
        $reservation->setQuantity($request->request->get('quantity'));
        $reservation->setCdate(new \DateTime());
        $reservation->setStatus('In waiting');
        $managerRegistry->getManager()->persist($reservation);
        $managerRegistry->getManager()->flush();


        return $this->json(['success' => true, 'message' => 'Votre réservation a bien été prise en compte']);

    }
    #[Route('/getuser/{token}', name: 'reserved_get', methods: ['GET'])]
    public function GetTUser($token , TokenRepository $tokenRepository , UserRepository $userRepository): Response

    {
        $session = $this->user;
        $token = $tokenRepository->findOneBy(['token_id' => $token]);
        if ($session->getRoles()[0] == 'ROLE_MERCHANT') {
            $user_id = $token->getUserId();
            $user= $userRepository->findOneBy(['id' => $user_id]);
            $data = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail()
            ];


            return $this->json(['success' => true, 'message' => 'Envoie des réservations au client', 'user' => $data]);
        }
        else {
            return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
        }

    }

    #[Route('/point/{id}/add', name: 'point_add', methods: ['POST'])]
    public function AddPoint(UserRepository $userRepository, ManagerRegistry $managerRegistry, Request $request): Response
    {
        $session = $this->user;
        $points = $request->toArray();
        $user = $userRepository->findOneBy(['id' => $session->getId()]);
        $user->setLoyaltyPoints($user->getLoyaltyPoints() + $points['points']);

        $data = [
            'points de fidélité' => $user->getLoyaltyPoints(),
        ];

        return $this->json(['success' => true, 'message' => '', 'reservation' => $data]);




    }

    #[Route('/point/{id}/remove', name: 'point_delete', methods: ['POST'])]
    public function DeletePoint(UserRepository $userRepository, ManagerRegistry $managerRegistry, Request $request): Response
    {
        $session = $this->user;
        $user = $userRepository->findOneBy(['id' => $session->getId()]);
        $points = $request->toArray();
        $point = intval($points['points']);
        $Fpoint = $user->getLoyaltyPoints() - $point;
        if ($Fpoint < 0) $Fpoint = 0;
        $user->setLoyaltyPoints($Fpoint);
        $managerRegistry->getManager()->persist($user);
        $managerRegistry->getManager()->flush();
        $data = [
            'points de fidélité' => $user->getLoyaltyPoints(),
        ];
        return $this->json(['success' => true, 'amounts' => $data]);

    }

    #[Route('/shop/{id}/reservations', name: 'reserved_get', methods: ['GET'])]
    public function ReservedGet(Commerce $commerce, ManagerRegistry $managerRegistry): Response

    {

        $session = $this->user;
        if ($session->getRoles()[0] == 'ROLE_MERCHANT') {
            $reservations = $commerce->getReservations();
            $data = array();

            foreach ($reservations as $r) {
                if ($r->getCdate() < new \DateTime("1day ago"))
                {
                    $managerRegistry->getManager()->remove($r);
                    $managerRegistry->getManager()->flush();
                    return $this->json(['success' => false, 'message' => 'Votre réservation a expiré']);

                }

                $data[] = [

                    'id' => $r->getId(),
                    'product' => $r->getProduct()->getId(),
                    'quantity' => $r->getQuantity(),
                    'date' => $r->getCdate(),
                ];
            }

            return $this->json(['success' => true, 'message' => 'Voici les réservations de vos clients', 'reservation' => $data]);
        }
        return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
    }


    #[Route('/shop/reservation/{id}/modify', name: 'modify_reservation', methods: ['PUT', 'POST'])]
    public function pot_reserved(Request $request, ReservationRepository $reservationRepository, $id, ManagerRegistry $managerRegistry): Response

    {
        $session = $this->user;

        $shop_reservation_id = $reservationRepository->findOneBy(['id' => $id]);
        if ($session->getRoles()[0] == 'ROLE_MERCHANT') {
            $status = $request->request->get('status');
            if ($status)
            {
                $shop_reservation_id->setStatus($status);
                date_default_timezone_set('Europe/Paris');
                $date = new \DateTime();

                $shop_reservation_id->setCdate($date);
                $managerRegistry->getManager()->persist($shop_reservation_id);
                $managerRegistry->getManager()->flush();

                return $this->json(['success' => true, 'message' => 'La reservation a bien été modifier']);
            }
            else
            {
                return $this->json(['success' => false, 'message' => 'Vous devez renseigner un status']);
            }




        } else {
            return $this->json(['success' => false, 'message' => "Vous n'avez pas les droits pour accéder à cette page car vous êtes pas un marchand"]);
        }
    }

        #[Route('/shop/reservation/{id}/delete', name: 'delete_reservation' , methods: ['DELETE'])]
    public function delete_reserved(ReservationRepository $reservationRepository, $id, ManagerRegistry $managerRegistry): Response
    {
        $session = $this->user;

        $shop_reservation_id = $reservationRepository->findOneBy(['id' => $id]);
            $managerRegistry->getManager()->remove($shop_reservation_id);
            $managerRegistry->getManager()->flush();

            return $this->json(['success' => true, 'message' => 'La reservation a bien été supprimer']);



    }

    #[Route('/reservation', name: 'get_reservation', methods: ['GET'])]
    public function get_reserved(ReservationRepository $reservationRepository, ManagerRegistry $managerRegistry): Response

    {
        $data = array();
        $session = $this->user;

        $reservations = $reservationRepository->findBy(['user' => $session]);
        foreach ($reservations as $r) {
            if ($r->getCdate() < new \DateTime("1day ago"))
            {
                $managerRegistry->getManager()->remove($r);
                $managerRegistry->getManager()->flush();
                return $this->json(['success' => false, 'message' => 'Votre réservation a expiré']);

            }
            $data[] = [
                'id' => $r->getId(),
                'product' => $r->getProduct()->getId(),
                'quantity' => $r->getQuantity(),
                'date' => $r->getCdate(),
            ];
        }
        return $this->json(['success' => true, 'message' => 'Voici vos réservations', 'reservation' => $data]);
    }


    #[Route('/logout', name: 'disconect' , methods: ['GET'])]
    public function disconnect(Request $request, ManagerRegistry $managerRegistry, TokenRepository $tokenRepository): Response
    {
        $user = $this->user;
        $Mytoken = $tokenRepository->findOne(['user_id' => $user->getId()]);
        $managerRegistry->getManager()->remove($Mytoken);
        $managerRegistry->getManager()->flush();



        return $this->json(['success' => true, 'message' => 'Vous êtes déconnecté']);
    }

    #[Route('/map', name: 'map' , methods: ['GET'])]
    public function map(Request $request, ManagerRegistry $managerRegistry, TokenRepository $tokenRepository , CommerceRepository $commerceRepository): Response
    {
        $adresse = $commerceRepository->findAll();
        $data = array();
        foreach ($adresse as $a)
        {
            $data[] = [
                'id' => $a->getId(),
                'address' => $a->getAdresse()

            ];
        }




        return $this->json(['success' => true, 'adresse' => $data]);
    }

   #[Route('/categories', name: 'app_categories', methods: ['GET'])]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $session = $this->user;

        if($session->getRoles()[0] == 'ROLE_ADMIN'){
            $datas = array();
            foreach ($categories as $c) {

                $datas[] = [

                    'id' => $c->getId(),
                    'name' => $c->getName(),
                    'description' => $c->getDescription(),
                    'url' => '/api/categories/' . $c->getId(),

                ];

            }


        }

        return $this->json(['success' => true, 'message' => 'Vous pouvez consulter les catégories', $datas]);


    }

    #[Route('/categories/{id}', name: 'app_categories_id', methods: ['GET'])]
    public function categoriesId(CategoryRepository $categoryRepository, $id): Response
    {
        $category = $categoryRepository->findOneBy(array('id' =>$id));
        $session = $this->user;

        if($session->getRoles()[0] == 'ROLE_ADMIN'){
            $data = array();


                $data[] = [

                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),

                ];



            return $this->json(['success' => true, 'message' => 'Vous pouvez consulter les catégories', 'categories' => $data]);

        }
        return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);



    }
    //modifié l'utilisateur
    #[Route('/user/{id}/modify', name: 'modify_user', methods: ['PUT', 'POST'])]
    public function UModify(UserRepository $userRepository, ManagerRegistry $managerRegistry, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $session = $this->user;
        $user = $userRepository->findOneBy(array('id' =>$session->getId()));
        if ($user){
            $form = $request->toArray();

            if ($form['email'] == null){
                $form['email'] = $user->getEmail();

            }
            if ($form['lastname'] == null){
                $form['lastname'] = $user->getLastname();

            }
            if ($form['firstname'] == null){
                $form['firstname'] = $user->getFirstname();

            }

            $user->setEmail($form['email']);
            $user->setLastname($form['lastname']);
            $user->setFirstname($form['firstname']);
            $managerRegistry->getManager()->persist($user);
            $managerRegistry->getManager()->flush();





            return $this->json(['success' => true, 'message' => 'Vous avez modifié votre profile']);

        }
        return $this->json(['success' => false, 'message' => 'Vous n\'êtes pas l\'user lié à ce compte']);



    }

    #[Route('/user/{id}/MDPmodify', name: 'modify_user', methods: ['PUT', 'POST'])]
    public function ModifyMDP(UserRepository $userRepository, ManagerRegistry $managerRegistry, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $session = $this->user;
        $user = $userRepository->findOneBy(array('id' =>$session->getId()));
        $form = $request->toArray();
        if ($user && $form['password']){

            $user->setPassword($passwordHasher->hashPassword($user, $form['password']));

            $managerRegistry->getManager()->persist($user);
            $managerRegistry->getManager()->flush();





            return $this->json(['success' => true, 'message' => 'Vous avez modifié votre mot de passe']);

        }
        return $this->json(['success' => false, 'message' => 'Vous n\'êtes pas l\'user lié à ce compte']);



    }

    #[Route('/shops', name: 'modify_user', methods: ['GET'])]
    public function GShops(CommerceRepository $commerceRepository): Response
    {

        $session = $this->user;
        $commerce = $commerceRepository->findAll();
        $data = array();
        foreach ($commerce as $c)
        {
            $data[] =
                [
                    'id' => $c->getId(),
                    'name' => $c->getName(),
                    'description' => $c->getDescription(),
                    'adresse' => $c->getAdresse(),
                ];

        }
        return $this->json(['success' => true, 'message' => 'Vous pouvez consulter les commerces', 'commerces' => $data]);




    }





}