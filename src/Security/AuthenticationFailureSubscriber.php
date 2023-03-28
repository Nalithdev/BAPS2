<?php

namespace App\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationFailureSubscriber implements EventSubscriberInterface
{
	
	public static function getSubscribedEvents(): array
	{
		return [
			'kernel.exception' => 'onKernelException',
		];
	}
	
	public function onKernelException(ExceptionEvent $event)
	{
		$exception = $event->getThrowable();
		
		if ($exception instanceof \Exception) {
			$response = new UnauthorizedHttpException('Bearer', $exception->getMessage());
			$event->setResponse(new JsonResponse(
				['message' => $response->getMessage(), 'code' => 401],
				$response->getStatusCode()));
		}
	}
}