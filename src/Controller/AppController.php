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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/login', name:'app_auth', methods: ['POST'])]
    public function auth(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher ): Response
    {
        $form = $request->toArray();
        $email = $form ['email'];

        $user = $userRepository->findOneBy(['email' => $email]);

        $session = $request->getSession();
        if ($user) {
            $password = $form['password'];
            if ($user->getPassword() == $passwordHasher->isPasswordValid($user, $password)) {
                $session->set('user', $user->getEmail());
                $this->addFlash('success', 'Vous êtes connecté');
                return $this->redirectToRoute('app_auth1' , [ 'id' => $user->getId()]);

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
        $form = $request->toArray();

        $firstname = $form['firstname'];
        $lastname = $form['lastname'];
        $email = $form['email'];
        $password = $form['password'];
        $action = $form['action'];
        $Nuser = new User();
        if ($firstname != null && $lastname != null && $email != null && $password != null) {
            $Nuser->setFirstname($firstname);
            $Nuser->setLastname($lastname);
            $Nuser->setEmail($email);
            $Nuser->setPassword($passwordHasher->hashPassword($Nuser, $password));
            if ($action == 'shop') {
                if ($request->request->get('siren') != null) {

                    $Nuser->setRoles(['ROLE_MERCHANT']);
                    $Nuser->setSiren($form['siren']);
                    $Nuser->setApproved(false);
                    $Nuser->setLoyaltyPoints(0);
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
    public function auth1(Request $request, UserRepository $userRepository, $id, ManagerRegistry $managerRegistry, TokenGeneratorInterface $tokenGenerator, TokenRepository $tokenRepository ): JsonResponse
    {
        // Je crée une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $fuser = $userRepository->findOneBy(['id' => $id]);
        $role = $fuser->getRoles();

        $Stoken = $tokenGenerator->generateToken();
        $Mytoken = $tokenRepository->findOneBy(['user_id' => $id]);

        if ($Mytoken) {
            return $this->json  (["success" => true ,'token' => $Mytoken , 'id' => $id, 'role' => $role[0]]);
        }
        //bonjour

        $Ntoken = new Token();
        $Ntoken->setTokenId($Stoken);
        $Ntoken->setUserId($id);
        $managerRegistry->getManager()->persist($Ntoken);
        $managerRegistry->getManager()->flush();

        //recuperer le token dans le storage



        return $this->json  (["success" => true ,'token' => $Stoken , 'id' => $id, 'role' => $role[0]]);

    }



    #[Route('/users', name: 'users' , methods: ['GET'])]
    public function users(Request $request , UserRepository $userRepository): Response

    {
        $criteria = new Criteria();
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');
        $criteria->setMaxResults($limit ?? 10);
        $criteria->setFirstResult($offset ?? 0);
        $users = $userRepository->matching($criteria);
        foreach ($users as $user) {
            if($user->getRoles()[0] == 'ROLE_MERCHANT' || $user->getRoles()[0] == 'ROLE_ADMIN'){

            }
            else{
                $data[] = [

                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'email' => $user->getEmail(),
                    'url' => '/api/user/'.$user->getId(),

                ];
            }

        }
        return $this->json(['success' => true, 'users' => $data]);
    }

}
