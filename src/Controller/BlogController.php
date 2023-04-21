<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog", name="portal_page_blog_")
 */
class BlogController extends BaseController
{
    protected BlogRepository $repository;

    public function __construct(
        BlogRepository $repository
    ){
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="list")
     */
    public function list(): Response
    {
        $entities = $this->repository->findListElements(false);

        return $this->render('portal/blog/list.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/{slug}", name="show")
     */
    public function show(string $slug): Response
    {
        $entity = $this->repository->findOneBy(['slug' => $slug]);

        return $this->render('portal/blog/show.html.twig', [
            'entity' => $entity
        ]);
    }
}
