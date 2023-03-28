<?php

namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\Feed;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CommerceRepository;
use App\Repository\FeedRepository;
use App\Repository\ProductRepository;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/private')]
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

		if(!$user) throw new UnauthorizedHttpException("Bearer", 'vous devez être connecté');
		return $this->user = $user;
	}
	
	// TOUTES LES ROUTES CI DESSOUS NÉCESSITERONT L'AJOUT D'UN TOKEN UTILISATEUR DANS LE HEADER DE LA REQUETE


    #[Route('/message', name:'app_SFeed', methods: ['POST'])]
    public function SendFeed(Request $request,  ManagerRegistry $managerRegistry): Response
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $id = $request->request->get('id');

        if (!$title || !$description) {
            return $this->json(['success' => false , 'message' => 'Veuillez remplir tous les champs']);
        }
        $dates =  date('Y-m-d H:i:s');
        ;
        $feed = new Feed();
        $feed->setTitle($title);
        $feed->setDescription($description);
        $feed->setUser($id);
        $feed->setCDate(strval($dates));

        $managerRegistry->getManager()->persist($feed);
        $managerRegistry->getManager()->flush();


        return $this->json(['success' => true , 'message' => 'Feed envoyer']);
    }



    #[Route('/message', name:'app_Feed' , methods: ['GET'])]
    public function Feed(FeedRepository $feedRepository , UserRepository $userRepository, Request $request): JsonResponse
    {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');
        $criteria = new Criteria() ;
        $criteria->orderBy(['id' => 'DESC']);
        $criteria->setMaxResults($limit ?? 50);
        $criteria->setFirstResult($offset ?? 0);
        $feed = $feedRepository->matching($criteria);

        $data = [];
        foreach ($feed as $f) {
            $user = $userRepository->findOneBy(['id' => $f->getUser()]);
            $data[] = [
                'id' => $f->getId(),
                'url' => '/api/private/message/'.$f->getId(),
            ];
        }

        return $this->json(['success' => true , 'data' => $data ]);
    }

    #[Route('/message/{id}', name:'app_Feed_id' , methods: ['GET'])]
    public function FeedId(FeedRepository $feedRepository , UserRepository $userRepository, $id , CommerceRepository $commerceRepository): JsonResponse
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
                'url' => '/api/private/shop/'.$shop->getId(),
            ],
            'date' => $feed->getCDate(),
        ];

        return $this->json(['success' => true , 'data' => $data ]);
    }

    #[Route('/shop', name:'app_DFeed' , methods: ['POST'])]
    public function CreateShop(Request $request , ManagerRegistry $managerRegistry ): JsonResponse
    {
        $user = $this->user;
        if ($user->getRoles()[0] == 'ROLE_MERCHANT'){
            $shop = new Commerce();
            $shop->setName($request->request->get('name'));
            $shop->setDescription($request->request->get('description'));
            $managerRegistry->getManager()->persist($shop);
            $user->setCommerce($shop);
            $managerRegistry->getManager()->flush();

            return $this->json(['success' => true , 'message' => 'Votre page Commerce a bien été créer']);
        }
        return $this->json(['success' => false , 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
    }


    #[Route('/product', name:'app_product' , methods: ['POST'])]
    public function product(ManagerRegistry $managerRegistry, Request $request , CommerceRepository $commerceRepository): Response
    {
        $user = $this->user;
        if ($user->getRoles()[0] == 'ROLE_MERCHANT'){

            $commerce = $user->getCommerce();
            $shop = $commerceRepository->findOneBy(['id' => $commerce]);


        $product = new Product();
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setPrice($request->request->get('price'));
        $product->setStock($request->request->get('stock'));
        $product->setShop($shop);
        $managerRegistry->getManager()->persist($product);
        $managerRegistry->getManager()->flush();

        return $this->json(['success' => true , 'message' => 'Produit envoyer', 'produit' => $product] );
        }
        return $this->json(['success' => false , 'message' => 'Vous n\'avez pas les droits pour accéder à cette page' ] );


    }

    #[Route('/shop/{id}', name: 'app_product_id', methods: ['GET'])]
    public function show(ProductRepository $productRepository , CommerceRepository $commerceRepository , $id ): Response
    {
        $shop = $commerceRepository->findOneBy(['id' => $id]);
        if ($shop == null){
            return $this->json(['success' => false , 'message' => 'Ce commerce n\'existe pas' ] );
        }


        $product = $productRepository->findBy(['shop' => $shop]);
            $Tshop = array();
            $Tmessage = array();
            $Tshoop= array();


        $Tshoop['shop']['id'] = $shop->getId();
        $Tshoop['shop']['name'] = $shop->getName();
        $Tshoop['shop']['description'] = $shop->getDescription();

            foreach ( $product as $f) {
                $Tmessage[] =[
                    'id' => $f->getId(),
                'title' => $f->getName(),
                'Description' => $f->getDescription(),
                'Price' => $f->getPrice(),
                'Stock' => $f->getStock(),
                ];







            }


        return $this->json(['success' => true , 'message' => 'Envoie du commerce et de leur produit au client', 'shop' => $Tshoop, 'product' => $Tmessage ] );
    }

    #[Route('/user/{id}', name: 'user' , methods: ['GET'])]
    public function user(UserRepository $userRepository, $id): Response

    {
        $session = $this->user;
        $users = $userRepository->findOneBy(['id' => $id]);
        if ($session->getRoles()[0] == 'ROLE_ADMIN' || $session>getId() == $users->getId()) {
            $data[] = [
                'id' => $users->getId(),
                'firstname' => $users->getFirstname(),
                'lastname' => $users->getLastname(),
                'email' => $users->getEmail(),
                'role' => $users->getRoles()[0],
            ];
            return $this->json(['success' => true, 'users' => $data]);
        }else{
            return $this->json(['success' => false, 'message' => 'Vous n\'avez pas les droits pour accéder à cette page']);
        }

    }


}