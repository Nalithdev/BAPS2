<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FeedRepository;
use App\Repository\UserRepository;
use App\Security\ApiError;
use App\Security\TokenAuthenticator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class SecuredRouteController extends AbstractController
{
	public User $user;
	
	/**
	 * @throws Exception
	 */
	public function __construct(TokenAuthenticator $tokenAuthenticator, RequestStack $requestStack)
	{
		$request = $requestStack->getCurrentRequest();
		$user = $tokenAuthenticator->getUser($request);
		if(!$user) ApiError::error_401('Aucun utilisateur trouvé');
		return $this->user = $user;
	}
	
	// TOUTES LES ROUTES CI DESSOUS NÉCESSITERONT L'AJOUT D'UN TOKEN UTILISATEUR DANS LE HEADER DE LA REQUETE
	
	#[Route('/test', name: 'app_test')]
	public function test(): Response
	{
		return $this->json(['success' => true, 'message' => 'Vous etes connecte' , 'user' => $this->user]);
	}
	
	#[Route('/feed', name:'app_Feed', methods: ['GET'])]
	public function Feed(FeedRepository $feedRepository , UserRepository $userRepository): JsonResponse
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
	
	#[Route('/feed', name:'app_Feed_send', methods: ['POST'])]
	public function sendFeed(){
		//TODO
	}
}