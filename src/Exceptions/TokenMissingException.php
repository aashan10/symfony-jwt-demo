<?php
/**
 * Created by PhpStorm.
 * User: aashanghimire
 * Date: 9/15/19
 * Time: 8:57 PM
 */

namespace App\Exceptions;

class TokenMissingException extends AbstractAuthenticationException
{
    public function __construct($message = 'Authentication token is required to access this url!')
    {
        parent::__construct($message, 'token_missing', 401);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStatusCode(){
        return $this->code;
    }

}