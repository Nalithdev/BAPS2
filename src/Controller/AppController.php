<?php

namespace App\Controller;


use App\Entity\Feed;
use App\Entity\Product;
use App\Entity\Token;
use App\Entity\User;
use App\Repository\FeedRepository;
use App\Repository\ProductRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Security\TokenAuthenticator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


#[Route('/api')]
class AppController extends AbstractController
{
    #[Route('/auth', name:'app_auth', methods: ['POST', 'GET'])]
    public function auth(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher ): Response
    {
        $email = $request->request->get('email');
        //$email = "francisbertrand@gmail.com";
        $user = $userRepository->findOneBy(['email' => $email]);
        //$user1 = $userRepository->findOneBy(['email' => 'user1@example.com']);



        $session = $request->getSession();
        if ($user) {
            $password = $request->request->get('password');
            //$password ="password";
            if ($user->getPassword() == $passwordHasher->isPasswordValid($user, $password)) {
                $session->set('user', $user->getEmail());
                $this->addFlash('success', 'Vous êtes connecté');
                return $this->redirectToRoute('app_auth1' , [ 'id' => $user->getId()]);

                //new JsonResponse(['success' => true, 'message' => 'Vous etes connecte' , $session->get('user')]);

            }

        }
        return $this->json(['success' => false, 'message' => 'Identifiants incorrects']);
    }
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);


    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $passwordHasher): Response
    {


        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $action = $request->request->get('action');
        $Nuser = new User();
        if ($firstname != null && $lastname != null && $email != null && $password != null) {
            $Nuser->setFirstname($firstname);
            $Nuser->setLastname($lastname);
            $Nuser->setEmail($email);
            $Nuser->setPassword($passwordHasher->hashPassword($Nuser, $password));
            if ($action == 'shop') {
                if ($request->request->get('siren') != null) {

                    $Nuser->setRoles(['ROLE_MERCHANT']);
                    $Nuser->setSiren($request->request->get('siren'));
                    $Nuser->setApproved(false);
                } else {
                    return new JsonResponse(['success' => false, 'message' => 'Vous devez renseigner un siren']);
                }
            } else {
                $Nuser->setRoles(['ROLE_USER']);

            }
            $managerRegistry->getManager()->persist($Nuser);
            $managerRegistry->getManager()->flush();
            return  new JsonResponse(['success' => true, 'message' => 'Vous etes inscrit']);
        }
        else{
            return new JsonResponse(['success' => false, 'message' => 'Vous devez renseigner tous les champs']);
        }





    }
    #[Route('/auth1/{id}', name: 'app_auth1')]
    public function auth1(Request $request, UserRepository $userRepository, $id, ManagerRegistry $managerRegistry, TokenGeneratorInterface $tokenGenerator ): JsonResponse
    {
        // Je crée une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $fuser = $userRepository->findOneBy(['id' => $id]);
        $role = $fuser->getRoles();
        $Stoken = $tokenGenerator->generateToken();

        $date = time();
        $Ntoken = new Token();
        $Ntoken->setTokenId($Stoken);
        $Ntoken->setUserId($id);
        $Ntoken->setCreateDate($date);
        $managerRegistry->getManager()->persist($Ntoken);
        $managerRegistry->getManager()->flush();

        //recuperer le token dans le storage



        return $this->json  (['token' => $Stoken , 'id' => $id, 'role' => $role[0]]);

    }

    #[Route('/feed/send', name:'app_SFeed', methods: ['POST'])]
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
	
	
	#[Route('/feed', name:'app_Feed')]
    public function Feed(Request $request, FeedRepository $feedRepository , UserRepository $userRepository, TokenAuthenticator $tokenAuthenticator): JsonResponse
    {
		
        $feed = $feedRepository->findAll();

        $Tfeed = array();
        $Tmessage = array();


        $x = 0;

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



    #[Route('/products', name:'app_product' , methods: ['POST', 'GET'])]
    public function product(ManagerRegistry $managerRegistry): Response
    {

        $product = new Product();
        $product->setName('name');
        $product->setDescription('description');
        $product->setPrice(rand(1, 100));
        $product->setStock(rand(1, 100));
        $managerRegistry->getManager()->persist($product);
        $managerRegistry->getManager()->flush();
        return $this->json(['success' => true , 'message' => 'Produit envoyer', 'produit' => $product] );



    }


}
