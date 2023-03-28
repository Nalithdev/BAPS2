<?php

namespace App\Security;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiError
{
	static function error_401($message)
	{
		throw new UnauthorizedHttpException(
			"Bearer",
			$message
		);
	}
}