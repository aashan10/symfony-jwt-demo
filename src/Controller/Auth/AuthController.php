<?php


namespace App\Controller\Auth;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exceptions\UserNotFoundException;

class AuthController
{

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @Route("/login")
     * @throws UserNotFoundException
     */
    public function login(Request $request, UserRepository $userRepository)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $userRepository->findOneBy(['username' => $username, 'password' => md5($password)]);
        if($user == null){
            throw new UserNotFoundException("Username and password don't match our records!");
        }

        if($user->getAccessToken() == null){
            $token = $user->generateAccessToken();
        }else{
            $token = $user->getAccessToken();
        }
        return JsonResponse::create([
            'status' => 'success',
            'data' => [
                'access_token' => $token,
                'user' => [
                    'username' => $user->getUsername(),
                    'id' => $user->getId(),
                    'created_at' => $user->getCreatedAt()
                ]
            ]
        ]);


    }

    public function register()
    {

        $date = new \DateTime();
    }
}