<?php

namespace App\Controller;

use App\Repository\AdminRepository;
use App\Repository\TraderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/auth', name: 'app_auth' , Methods: ['POST'])]
    public function index(UserRepository $userRepository, AdminRepository $adminRepository, TraderRepository $traderRepository): Response
    {


    }
}
