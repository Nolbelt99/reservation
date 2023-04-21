<?php

namespace App\Admin\Controller;

use App\Entity\Gallery;
use App\Entity\Service;
use App\Enum\ServiceTypeEnum;
use App\Entity\ServiceTranslation;
use App\Admin\Filter\ServiceFilter;
use App\Admin\Form\ServiceFormType;
use App\Service\LocalFileUploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/admin/szolgaltatasok", name="admin_service_")
 */
class ServiceController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{op}/list/{_locale}", name="list")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        FilterBuilderUpdater $filterBuilderUpdater,
        FormFactoryInterface $formFactory,
        TranslatableListener $translatableListener,
        string $op,
        $_locale
    ){
        if (!in_array(strtoupper($op), ServiceTypeEnum::getChoices())) {
            throw new NotFoundHttpException('Az oldal nem található');
        } else {
            $translatableListener->setTranslatableLocale($_locale);
            $queryBuilder = $this->entityManager->getRepository(Service::class)->findListElements($_locale, $op);
            $filterForm = $formFactory->create(ServiceFilter::class);
            if ($request->query->has($filterForm->getName())) {
                $filterForm->submit($request->query->get($filterForm->getName()));
                if ($filterForm->isValid()) {
                    $filterBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
                }
            }
            $entities = $this->orderedPaginatedList($request, $queryBuilder, $paginator);
            return $this->render('admin/service/list.html.twig', [
                'entities' => $entities,
                'op' => $op,
                'filterForm' => $filterForm->createView(),
            ]);
        }
    }

    /**
     * @Route("/{op}/form/{id}", name="form")
     */
    public function form(
        Request $request,
        TranslatableListener $translatableListener,
        ParameterBagInterface $param,
        LocalFileUploadManager $localFileUploadManager,
        string $op,
        $id = 0
    ){
        if (!in_array(strtoupper($op), ServiceTypeEnum::getChoices())) {
            throw new NotFoundHttpException('Az oldal nem található');
        } else {
            if ($id > 0) {
                $entity = $this->entityManager->getRepository(Service::class)->findOneBy(['id' => $id]);
                if (!$entity instanceof Service) {
                    throw $this->createNotFoundException('A szolgaáltatás nem található.');
                }
                $object = $entity;
            } else {
                $entity = new Service();
                $object = null;
            }

            $translatableListener->setTranslatableLocale($param->get('locale'));
            $message = '';
            $companiesJson = $this->getParameter('company_data');
            $companies = json_decode($companiesJson);

            $companies = [];
            foreach (json_decode($companiesJson) as $company) {
                $companies[] = $company->name;
            }
            $form = $this->createForm(ServiceFormType::class, $entity, ['op' => $op, 'id' => $id, 'companies' => $companies]);
            try {
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    if ($form->isValid()) {
                        $translations = $form->getData()->getTranslations()->getValues();
                        foreach ($translations as $translation) {
                            $slug = $this->entityManager->getRepository(ServiceTranslation::class)->findSlug($translation->getContent(), $object);
                            if (!empty($slug)) {
                                $message = 'A slugnak egyedinek kell lennie! ';
                                throw new ValidatorException();
                            }
                        }
                        $coverImage = $form->get('coverImage')->getData();
                        $coverImageCollection = $form->get('coverImageCollection')->getData();
                        if ($coverImage) {
                            if ($coverImageCollection) {
                                $newCoverImageCollection = $localFileUploadManager->upload($coverImageCollection, $param->get('file_upload_dir'));
                                $entity->setCoverImageCollection($newCoverImageCollection);
                            } else {
                                if (!$entity->getCoverImageCollection()) {
                                    $message = 'Kérem töltsön fel lista képet! ';
                                    throw new ValidatorException();
                                }
                            }
                            $newCoverImage = $localFileUploadManager->upload($coverImage, $param->get('file_upload_dir'));
                            $entity->setCoverImage($newCoverImage);
                        } else {
                            if (!$entity->getCoverImage()) {
                                $message = 'Kérem töltsön fel nyitó képet! ';
                                throw new ValidatorException();
                            }
                        }
                        $giftImage = $form->get('giftImage')->getData();
                        if ($giftImage) {
                            $newGiftImage = $localFileUploadManager->upload($giftImage, $param->get('file_upload_dir'));
                            $entity->setGiftImage($newGiftImage);
                        }
                        $galleryImages = $form->get('galleryImages')->getData();
                        if ($galleryImages) {
                            foreach ($galleryImages as $galleryImage) {
                                $newgalleryImage = $localFileUploadManager->upload($galleryImage, $param->get('file_upload_dir'));
                                $gallery = new Gallery();
                                $gallery->setPath($newgalleryImage);
                                $gallery->setService($entity);
                                $this->entityManager->persist($gallery);
                            }
                        }

                        
                        $companies = $this->getParameter('company_data');

                        foreach (json_decode($companies) as $company) {
                            if ($company->name == $form->get('companyName')->getData()) {
                                $entity->setCompanyPriority($company->priority);
                            }
                        }

                        $this->entityManager->persist($entity);
                        $this->entityManager->flush();
                        $this->addFlash('notice', 'Sikeres rögzítés.');
                        if ($id == 0 && $entity->getId()) {
                            return $this->redirectToRoute('admin_service_form', ['id' => $entity->getId(), 'op' => $op]);
                        }
                    } else {
                        $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
                    }
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Sikertelen rögzítés. ' . $message . $e->getMessage());
            }

            return $this->render('admin/service/form.html.twig', [
                'form' => $form->createView(),
                'entity' => $entity,
                'op' => $op,
            ]);
        }
    }

    /**
     * @Route("/{op}/remove/{id}", name="remove")
     */
    public function remove($id, $op)
    {
        if (!in_array(strtoupper($op), ServiceTypeEnum::getChoices())) {
            throw new NotFoundHttpException('Az oldal nem található');
        } else {
            $entity = $this->entityManager->getRepository(Service::class)->findOneBy(['id' => $id]);
            if (!$entity instanceof Service) {
                throw $this->createNotFoundException('A szolgáltatás nem található.');
            }
            try {
                $entity->setDeleted(true);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->addFlash('notice', 'Sikeres törlés.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Sikertelen törlés.');
            }
            return $this->redirectToRoute('admin_service_list', ['op' => $op]);
        }
    }
}
