<?php

namespace App\Admin\Controller;

use App\Entity\Blog;
use App\Admin\Filter\BlogFilter;
use App\Admin\Form\BlogFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;

/**
 * @Route("/admin/blogok", name="admin_blog_")
 */
class BlogController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/list", name="list")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        FilterBuilderUpdater $filterBuilderUpdater,
        FormFactoryInterface $formFactory
    ){
        $queryBuilder = $this->entityManager->getRepository(Blog::class)->findListElements(true);
        $filterForm = $formFactory->create(BlogFilter::class);
        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            if ($filterForm->isValid()) {
                $filterBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
            }
        }
        $entities = $this->orderedPaginatedList($request, $queryBuilder, $paginator);
        return $this->render('admin/blog/list.html.twig', [
            'entities' => $entities,
            'filterForm' => $filterForm->createView(),
        ]);
        
    }

    /**
     * @Route("/form/{id}", name="form")
     */
    public function form(
        Request $request,
        $id = null
    ){
        if ($id) {
            $entity = $this->entityManager->getRepository(Blog::class)->findOneBy(['id' => $id]);
            if (!$entity instanceof Blog) {
                throw $this->createNotFoundException('A blog bejegyzés nem található.');
            }
        } else {
            $entity = new Blog();
        }

        $form = $this->createForm(BlogFormType::class, $entity);
        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->isValid()) {
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->addFlash('notice', 'Sikeres rögzítés.');
                    if (is_null($id) && $entity->getId()) {
                        return $this->redirectToRoute('admin_blog_form', ['id' => $entity->getId()]);
                    }
                } else {
                    $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen rögzítés.');
        }

        return $this->render('admin/blog/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id)
    {

        $entity = $this->entityManager->getRepository(Blog::class)->findOneBy(['id' => $id]);
        if (!$entity instanceof Blog) {
            throw $this->createNotFoundException('A blog bejegyzés nem található.');
        }
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->addFlash('notice', 'Sikeres törlés.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen törlés.');
        }
        return $this->redirectToRoute('admin_blog_list');
        
    }
}
