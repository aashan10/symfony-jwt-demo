<?php

namespace App\Exceptions;

use App\Exceptions\AbstractAuthenticationException;

class UnknownTokenException extends AbstractAuthenticationException{
    public function __construct($message = "Token issued by unknown source!")
    {
        parent::__construct($message, 'unknown_token_issuer', 401);
    }
}