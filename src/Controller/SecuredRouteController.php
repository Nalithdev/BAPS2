<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\TokenAuthenticator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
		if(!$user) throw new UnauthorizedHttpException("Bearer", 'vous devez être connecté');
		return $this->user = $user;
	}
	
	// TOUTES LES ROUTES CI DESSOUS NÉCESSITERONT L'AJOUT D'UN TOKEN UTILISATEUR DANS LE HEADER DE LA REQUETE
	
	#[Route('/test', name: 'app_test')]
	public function test(): Response
	{
		return $this->json(['success' => true, 'message' => 'Vous etes connecte' , 'user' => $this->user]);
	}
	
}