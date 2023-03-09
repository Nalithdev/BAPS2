<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Tests\tmp\current_project_xml\src\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/auth1', name: 'app_auth1', methods: ['GET','POST'])]
    public function auth(Request $request, UserRepository $userRepository , $id): Response
    {
        // Je vais créer une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $users = $userRepository->findBy($id);



        // Je génère le token
        $token = new UsernamePasswordToken($users, 'main', $users->getRoles());


        // stocker le jeton généré dans la session
        $session = $request->getSession();
        $session->set('_security_main', serialize($token));


        // retourner le token
        return $this->json(['token' => $token]);
    }
}
