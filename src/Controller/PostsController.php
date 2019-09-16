<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @param $id
     * @Route("/posts/{id}")
     */
    public function show($id)
    {

    }

    /**
     * @Route("/posts")
     */
    public function index()
    {
        dump("This is postsCOntroller");
        return JsonResponse::create([]);
    }
}