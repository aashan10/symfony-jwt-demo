<?php

namespace App\Subscribers;


use App\Contracts\TokenAuthenticatedControllerInterface;
use App\Exceptions\AbstractAuthenticationException;
use App\Exceptions\TokenMissingException;
use App\Exceptions\UnknownTokenException;
use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Firebase\JWT\JWT;

class TokenSubscriber implements EventSubscriberInterface
{
    protected $repository;
    protected $request;
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }
    public function isAuthRequest( ControllerEvent $event ){

        $controller = $event->getController();

        if(!is_array($controller)){
            return;
        }

        if($controller[0] instanceof TokenAuthenticatedControllerInterface){
            $token = $event->getRequest()->query->get('_token');
            if($token == null || $token == '') {
                throw new TokenMissingException();
            }else{
                try{
                   $jwt = JWT::decode($token, $_ENV['APP_SECRET'], ['HS256', 'HS384', 'HS512', 'RS256', 'RS384', 'RS512']);
                    if($jwt->iss != 'jwt_authenticator'){
                        throw new UnknownTokenException();
                    };
                    $user = $this->repository->find($jwt->sub);
                    if($user == null){
                        throw new UserNotFoundException("The token doesn't belong to a user!");
                    }else{
                        $event->getRequest()->request->add(['auth' => $user]);
                    }
                }catch (\Exception $e){
                    throw $e;
                }
            }
            return;
        }
        return;
    }

    public static function getSubscribedEvents(){
        return [
            KernelEvents::CONTROLLER => 'isAuthRequest',
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event)
    {
        $exception = $event->getException();

        if($exception instanceof AbstractAuthenticationException){
            $event->setResponse( JsonResponse::create([
                'status' => 'error',
                'type' => $exception->getType(),
                'message' => $exception->getMessage()
            ], $exception->getCode()));

        }else{
            switch ($exception){
                case $exception instanceof HttpExceptionInterface :
                    $code = $exception->getStatusCode();
                    break;
                default :
                    $code = 500;
                    break;

            }
            $exception = explode('\\',  get_class($event->getException()));
            $name = array_pop($exception);
            $event->setResponse( JsonResponse::create([
                'status' => 'error',
                'type' => $name,
                'message' => $event->getException()->getMessage()
            ], $code) );
        }

    }

}