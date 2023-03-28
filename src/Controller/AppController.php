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
        $user = $userRepository->findOneBy(['email' => $email]);

        $session = $request->getSession();
        if ($user) {
            $password = $request->request->get('password');
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
    public function auth1(Request $request, UserRepository $userRepository, $id, ManagerRegistry $managerRegistry, TokenGeneratorInterface $tokenGenerator, TokenRepository $tokenRepository ): JsonResponse
    {
        // Je crée une instance User dans laquelle je lui demande de chercher les id des utilisateurs
        $fuser = $userRepository->findOneBy(['id' => $id]);
        $role = $fuser->getRoles();
        $Mytoken = $tokenRepository->findOneBy(['userId' => $id]);
        if ($Mytoken) {
            $managerRegistry->getManager()->remove($Mytoken);
            $managerRegistry->getManager()->flush();
        }
        $Stoken = $tokenGenerator->generateToken();

        $date = time();
        $Ntoken = new Token();
        $Ntoken->setTokenId($Stoken);
        $Ntoken->setUserId($id);
        $Ntoken->setCreateDate($date);
        $managerRegistry->getManager()->persist($Ntoken);
        $managerRegistry->getManager()->flush();

        //recuperer le token dans le storage



        return $this->json  (['header' => ['code' => 200 , 'message' => 'Vous êtes connectés'] ,'token' => $Stoken , 'id' => $id, 'role' => $role[0]]);

    }

}
