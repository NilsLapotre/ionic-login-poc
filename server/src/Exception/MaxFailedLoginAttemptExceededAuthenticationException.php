<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class MaxFailedLoginAttemptExceededAuthenticationException extends CustomUserMessageAuthenticationException
{
    private $messageKey;

    private $messageData = [];

    public function __construct(string $message = '', array $messageData = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);

        $this->setSafeMessage($message, $messageData);
    }
}
