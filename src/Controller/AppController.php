<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppController extends AbstractController
{
    #[Route('/auth', name:'app_auth', methods: ['POST', 'GET'])]
    public function auth(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);
        //$user1 = $userRepository->findOneBy(['email' => 'user1@example.com']);



        $session = $request->getSession();
        if ($user) {
            $password = $request->request->get('password');
            if ($user->getPassword() == $passwordHasher->isPasswordValid($user, $password)) {
                $session->set('user', $user->getEmail());
                $this->addFlash('success', 'Vous êtes connecté');
                $this->redirectToRoute('app_auth1' , [ 'id' => $user->getId()]);
                $role = $user->getRoles();
                return  new JsonResponse(['success' => true, 'message' => 'Vous etes connecte' , $session->get('user')]);

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
    public function register(Request $request, \Doctrine\Persistence\ManagerRegistry $managerRegistry, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
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
    public function auth1(Request $request, UserRepository $userRepository, $id ): JsonResponse
    {
        // Je crée une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $fuser = $userRepository->findOneBy(['id' => $id]);
        $role = $fuser->getRoles();
        $email = $fuser->getEmail();
        $password = $fuser->getPassword();
        $all = [$email, $password , $role];

        // générer le token
        $token = new UsernamePasswordToken( $fuser, 'main', $role);



        // stocker le jeton généré dans la session
        $session = $request->getSession();


            $session->set('_security_name', $token);

            dd($session , $token);
        // retourner le token
        return new JsonResponse(['token' => $session->get('_security_name'), 'success' => true, 'message' => 'Vous etes connecte']);

    }






    }
