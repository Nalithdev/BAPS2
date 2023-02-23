<?php

namespace App\Controller;

use App\Repository\AdminRepository;
use App\Repository\TraderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends AbstractController
{
    #[Route('/auth', name:'app_auth', methods: ['POST', 'GET'])]
    public function auth(Request $request, UserRepository $userRepository, AdminRepository $adminRepository, TraderRepository $traderRepository, AuthenticationUtils $authenticationUtils , UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);
        $admin = $adminRepository->findOneBy(['email' => $email]);
        $trader = $traderRepository->findOneBy(['email' => $email]);
        $user1 = $userRepository->findOneBy(['email' => 'user1@example.com']);



        $session = $request->getSession();
        if ($user1) {
            $password = $request->request->get('password');

            //dd($passwordHasher->isPasswordValid($user1, 'password'));
            if ($user->getPassword() == $passwordHasher->isPasswordValid($user, $password)) {
                $session->set('user', $user);
                $this->addFlash('success', 'Vous êtes connecté');
                return $this->json(['success' => true, 'message' => 'Vous êtes connecté']);
            }

        } elseif ($admin) {
            if ($admin->getPassword() == $request->request->get('password')) {
                $session->set('admin', $admin);
                $this->addFlash('success', 'Vous êtes connecté');
                return $this->redirectToRoute('admin_index');
            }
        } elseif ($trader) {
            if ($trader->getPassword() == $request->request->get('password')) {
                $session->set('trader', $trader);
                $this->addFlash('success', 'Vous êtes connecté');
                return $this->redirectToRoute('trader_index');
            }
        }
        return $this->json(['success' => false, 'message' => 'Identifiants incorrects']);
    }
}