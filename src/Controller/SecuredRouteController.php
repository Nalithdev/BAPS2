<?php

namespace App\Controller;

use App\Entity\Commerce;
use App\Entity\Feed;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CommerceRepository;
use App\Repository\FeedRepository;
use App\Repository\UserRepository;
use App\Security\TokenAuthenticator;
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
	public function construct(TokenAuthenticator $tokenAuthenticator, RequestStack $requestStack)
	{
		$request = $requestStack->getCurrentRequest();

		$user = $tokenAuthenticator->getUser($request);
		if(!$user) throw new UnauthorizedHttpException("Bearer", 'vous devez être connecté');
		return $this->user = $user;
	}
	
	// TOUTES LES ROUTES CI DESSOUS NÉCESSITERONT L'AJOUT D'UN TOKEN UTILISATEUR DANS LE HEADER DE LA REQUETE


    #[Route('/feed/register', name:'app_SFeed', methods: ['POST'])]
    public function SendFeed(Request $request,  ManagerRegistry $managerRegistry): Response
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $id = $request->request->get('id');

        if (!$title && !$description) {
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


    #[Route('/feed/get', name:'app_Feed' ,methods: ['GET'])]
    public function Feed(FeedRepository $feedRepository , UserRepository $userRepository): JsonResponse
    {

        $feed = $feedRepository->findAll();

        $Tfeed = array();
        $Tmessage = array();

        foreach ( $feed as $f){
            $user = $userRepository->findOneBy(['id' => $f->getUser()]);

            $Tmessage['message']['id'] = $f->getId();
            $Tmessage['message']['title'] = $f->getTitle();
            $Tmessage['message']['Description'] = $f->getDescription();
            $Tmessage['message']['Date'] = $f->getCDate();
            $Tmessage['user']['id'] = $user->getId();
            $Tmessage['user']['firstname'] = $user->getFirstname();
            $Tmessage['user']['lastname'] = $user->getLastname();
            $Tmessage['user']['email'] = $user->getEmail();
            $Tmessage['user']['siren'] = $user->getSiren();
            $Tmessage['user']['roles'] = $user->getRoles();



            array_push($Tfeed,  $Tmessage);

        }

        return $this->json(['success' => true , 'message' => 'Feed envoyer', 'Feed' => $Tfeed ]);
    }

    #[Route('/shop/create', name:'app_DFeed')]
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


    #[Route('/product/register', name:'app_product' , methods: ['POST', 'GET'])]
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

    #[Route('/shop/send/{id}', name: 'app_product_id', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->json(['success' => true , 'message' => 'Produit envoyer', 'produit' => $product] );
    }



}