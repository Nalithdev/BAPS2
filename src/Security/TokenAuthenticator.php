<?php

namespace App\Security;
use App\Entity\User;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class TokenAuthenticator
{
	
	const ERROR = ['success' => false , 'code' => 401, 'message' => 'Vous devez être connecté'];
	
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

            //chercher dans le paramètre get "token"
            $token = $request->query->get('token');
            if(!$token) {
                return null;
            }
        }

		$result = $this->tokenRepository->findOneBy(['token_id' => $token]);


       return $result ? $this->userRepository->findOneBy(['id' => $result->getUserId()]) : null;

	}
}