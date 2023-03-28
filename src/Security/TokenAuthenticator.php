<?php

namespace App\Security;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TokenAuthenticator
{
	public function __construct(
		private TokenRepository $tokenRepository,
		private UserRepository $userRepository
	)
	{
	}
	
	
	/**
	 * @throws Exception
	 */
	public function getUser(Request $request): ?User
	{
		$token = $request->headers->get('Token');
		if(!$token) {
			$token = $request->query->get('token');
			
			if(!$token) ApiError::error_401('Aucun token fourni');
		};
		
		$result = $this->tokenRepository->findOneBy(['token_id' => $token]);
		
		if ($result) {
			$created = new \DateTime;
			$created->setTimestamp($result->getCreateDate());
			if ($created < new \DateTime('-1 week')) {
				$this->tokenRepository->remove($result);
				return null;
			}
			
			return $this->userRepository->findOneBy(['id' => $result->getUserId()]);
		}
		
		return null;
	}
}