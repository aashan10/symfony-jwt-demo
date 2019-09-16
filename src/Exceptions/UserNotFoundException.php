<?php


namespace App\Exceptions;


class UserNotFoundException extends AbstractAuthenticationException
{
    public function __construct($message = "User not found!")
    {
        parent::__construct($message, 'not_found', 404);
    }
}