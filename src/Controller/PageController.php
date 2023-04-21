<?php

namespace App\Controller;

use App\Enum\ServiceTypeEnum;
use App\Entity\ReservationItem;
use App\Form\ReservationItemType;
use App\Repository\PageRepository;
use App\Service\ReservationManager;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use App\Repository\PageTranslationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/{_locale}/oldalak", requirements={"_locale": "hu|en|de|"}, name="portal_page_")
 */
class PageController extends BaseController
{
    /**
     * @Route("/{slug}", name="show")
     */
    public function show(
        string $slug,
        TranslatableListener $translatableListener,
        Request $request,
        PageRepository $repository,
        PageTranslationRepository $translationRepository,
    ): Response {
        $_locale = $request->getLocale();
        $translatableListener->setTranslatableLocale($_locale);
        $translation = $translationRepository->findOneBySlug($slug);
        if (!$translation) {
            throw new NotFoundHttpException('Az oldal nem található');
        }
        foreach ($translation->getObject()->getTranslations()->getValues() as $value) {
            if ($value->getLocale() == $_locale && $value->getField() == "slug") {
                $entity = $repository->findBySlug($_locale, $value->getContent());
            }
        }
        if (!$entity) {
            throw new NotFoundHttpException('Az oldal nem található');
        }

        return $this->render('portal/page/show.html.twig', [
            'entity' => $entity
        ]);
    }
}
