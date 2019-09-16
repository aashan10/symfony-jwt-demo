<?php


namespace App\Controller\Admin;


use App\Contracts\TokenAuthenticatedControllerInterface;
use App\Entity\Posts;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController implements TokenAuthenticatedControllerInterface
{
    /**
     * @param PostsRepository $postsRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @Route("/admin/posts/new")
     */
    public function create(PostsRepository $postsRepository, EntityManagerInterface $manager, Request $request)
    {
        $post = new Posts();
        $post->setAuthorId(1);
        $post->setCreatedAt(new \DateTime());
        $post->setTitle('Test Title');
        $post->setDescription("Test Description");
        $manager->persist($post);
        $manager->flush();
        return JsonResponse::create([
            'status' => 'success',
            'data' => [
                'posts' => $postsRepository->findAll(),
                'count' => count($postsRepository->findAll())
            ]
        ]);
    }

    /**
     * @Route("/admin/posts")
     */
    public function index()
    {
        return JsonResponse::create([]);
    }

    /**
     * @param $id
     * @Route("/admin/posts/update/{id}")
     */
    public function update($id)
    {

    }
}