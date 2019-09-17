<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{

    /**
     * @Route("/posts/{author_id}")
     * @param $author_id
     * @param PostsRepository $postsRepository
     * @return JsonResponse
     */
    public function index($author_id, PostsRepository $postsRepository)
    {
        $posts = $postsRepository
            ->where('title', '=', 'Test Title')
            ->where('author_id', '=', $author_id)
            ->paginate(10)->get();

        if(count($posts) == 0){
            throw new NotFoundHttpException();
        }
        return JsonResponse::create([
            'status' => 'success',
            'data' => [
                'posts' => $posts
            ]
        ]);
    }
}