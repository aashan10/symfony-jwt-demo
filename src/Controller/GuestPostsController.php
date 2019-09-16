<?php
/**
 * Created by PhpStorm.
 * User: aashanghimire
 * Date: 9/15/19
 * Time: 8:16 PM
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GuestPostsController extends AbstractController
{
    /**
     * @Route("/posts/all")
     */
    public function view()
    {

        return JsonResponse::create([]);

    }

}