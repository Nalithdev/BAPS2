<?php

namespace App\Controller;

use App\Entity\User ;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthController extends AbstractController
{
    #[Route('/auth1', name: 'app_auth1', methods: ['POST'])]
    public function auth(Request $request, User $userRepository, $id ): Response
    {
        // Je crée une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $users = $userRepository->findby($id);


        // générer le token
        $token = new UsernamePasswordToken($users, null, $users->getRoles());


        // stocker le jeton généré dans la session
        $session = $request->getSession();
        if ($session) {
            $session->set('_security_main', serialize($token));
        }

        // retourner le token
        return $this->json(['token' => $token]);
    }
}
