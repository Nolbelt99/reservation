<?php

namespace App\Admin\Controller;

use App\Entity\Page;
use App\Entity\PageTranslation;
use App\Admin\Filter\PageFilter;
use App\Admin\Form\PageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/admin/oldalak", name="admin_page_")
 */
class PageController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/list/{_locale}", name="list")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        FilterBuilderUpdater $filterBuilderUpdater,
        FormFactoryInterface $formFactory,
        TranslatableListener $translatableListener,
        $_locale
    ){
        $translatableListener->setTranslatableLocale($_locale);
        $queryBuilder = $this->entityManager->getRepository(Page::class)->findListElements($_locale);
        $filterForm = $formFactory->create(PageFilter::class);
        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            if ($filterForm->isValid()) {
                $filterBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
            }
        }
        $entities = $this->orderedPaginatedList($request, $queryBuilder, $paginator);
        return $this->render('admin/page/list.html.twig', [
            'entities' => $entities,
            'filterForm' => $filterForm->createView(),
        ]);
        
    }

    /**
     * @Route("/form/{id}", name="form")
     */
    public function form(
        Request $request,
        TranslatableListener $translatableListener,
        ParameterBagInterface $param,
        $id = null
    ){
        if ($id) {
            $entity = $this->entityManager->getRepository(Page::class)->findOneBy(['id' => $id]);
            if (!$entity instanceof Page) {
                throw $this->createNotFoundException('Az oldal nem található.');
            }
            $object = $entity;
        } else {
            $entity = new Page();
            $object = null;
        }

        $message = '';
        $translatableListener->setTranslatableLocale($param->get('locale'));
        $form = $this->createForm(PageFormType::class, $entity);
        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->isValid()) {
                    $translations = $form->getData()->getTranslations()->getValues();
                    foreach ($translations as $translation) {
                        $slug = $this->entityManager->getRepository(PageTranslation::class)->findSlug($translation->getContent(), $object);
                        if (!empty($slug)) {
                            $message = 'A slugnak egyedinek kell lennie! ';
                            throw new ValidatorException();
                        }
                    }
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->addFlash('notice', 'Sikeres rögzítés.');
                    if (is_null($id) && $entity->getId()) {
                        return $this->redirectToRoute('admin_page_form', ['id' => $entity->getId()]);
                    }
                } else {
                    $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen rögzítés. ' . $message);
        }

        return $this->render('admin/page/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id, $op)
    {

        $entity = $this->entityManager->getRepository(Page::class)->findOneBy(['id' => $id]);
        if (!$entity instanceof Page) {
            throw $this->createNotFoundException('Az oldal nem található.');
        }
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->addFlash('notice', 'Sikeres törlés.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen törlés.');
        }
        return $this->redirectToRoute('admin_page_list', ['op' => $op]);
        
    }
}
