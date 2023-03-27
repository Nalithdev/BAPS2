<?php

namespace App\Controller;


use App\Entity\Feed;
use App\Entity\Token;
use App\Entity\User;
use App\Repository\FeedRepository;
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
	
	/**
	 * @throws Exception
	 */
	#[Route('/feed', name:'app_Feed')]
    public function Feed(Request $request, FeedRepository $feedRepository , UserRepository $userRepository, TokenAuthenticator $tokenAuthenticator): JsonResponse
    {
		$token = $request->headers->get('Token');
		if(!$token) {
			return $this->json($tokenAuthenticator::$JSON_ERROR);
		}
		
		if(!$tokenAuthenticator->getUser($token)) return $this->json($tokenAuthenticator::$JSON_ERROR);
		
        $feed = $feedRepository->findAll();
        $Tlfeed =  array();
        $Tfeed = array();

        $x = 0;

        foreach ( $feed as $f){
            $user = $userRepository->findOneBy(['id' => $f->getUser()]);
            $Tlfeed['id'] = $user->getId();
            $Tlfeed['title'] = $f->getTitle();
            $Tlfeed['Description'] = $f->getDescription();
            $Tlfeed['Date'] = $f->getCDate();
            $Tlfeed['FN'] = $user->getFirstname();
            $Tlfeed['LN'] = $user->getLastname();
            array_push($Tfeed,  $Tlfeed);
        }

        return $this->json(['success' => true , 'message' => 'Feed envoyer', 'Feed' => $Tfeed ]);
    }

    #[Route('/check', name:'app_check' , methods: ['POST', 'GET'])]
    public function check(Request $request , TokenRepository $tokenRepository, ManagerRegistry $managerRegistry): JsonResponse
    {
        $token = $request->request->get('token');
        $tokens= "bCYLQwKclswfzm1PqmPl44i1_ux-rc2h5PMXBWuIQyY";
        $Mytoken = $tokenRepository->findOneBy(['token_id' => $token]);
        $date = time();
        $day = ($date - $Mytoken->getCreateDate())/86400;
        if ($day < 7){
            return $this->json(['success' => true , 'message' => 'Token valide, donc connecition autorisé']);
        }
        else{
            $managerRegistry->getManager()->remove($Mytoken);
            return $this->json(['success' => false , 'message' => 'Token invalide, donc connecition non autorisé']);
        }



    }



}
