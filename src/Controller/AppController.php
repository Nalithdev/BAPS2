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
        $Nuser->setFirstname($firstname);
        $Nuser->setLastname($lastname);
        $Nuser->setEmail($email);
        $Nuser->setPassword($passwordHasher->hashPassword($Nuser ,$password));
        if ($action == 'shop'){
            $Nuser->setRoles(['ROLE_SHOP']);
            $Nuser->setSiren($request->request->get('siren'));
            $Nuser->setApproved(false);
        }
        else{
            $Nuser->setRoles(['ROLE_USER']);
        }
        $managerRegistry->getManager()->persist($Nuser);
        $managerRegistry->getManager()->flush();
        return  new JsonResponse(['success' => true, 'message' => 'Vous etes inscrit']);

    }






    }
