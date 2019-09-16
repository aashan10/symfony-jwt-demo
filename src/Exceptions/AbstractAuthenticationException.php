<?php
/**
 * Created by PhpStorm.
 * User: aashanghimire
 * Date: 9/15/19
 * Time: 9:19 PM
 */

namespace App\Exceptions;


use App\Contracts\AuthenticationExceptionInterface;

class AbstractAuthenticationException extends \Exception implements AuthenticationExceptionInterface
{

    protected $type;
    protected $code;


    public function __construct($message, $type, $status_code)
    {
        parent::__construct($message);
        $this->code = $status_code;
        $this->type = $type;
    }

    public function getType(){
        return $this->type;
    }


}