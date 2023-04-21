<?php

namespace App\Controller;

use App\Entity\Receipt;
use App\Entity\User;
use App\Form\UserFormType;
use App\Entity\Reservation;
use App\Entity\ReservationItem;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profil/{_locale}", requirements={"_locale": "hu|en|de|"}, name="portal_page_profile_")
 */
class ProfileController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{id}", name="form")
     */
    public function show(Request $request, int $id, TranslatableListener $translatableListener): Response
    {
        $_locale = $request->getLocale();
        $translatableListener->setTranslatableLocale($_locale);
        $entity = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if (!$entity instanceof User) {
            throw $this->createNotFoundException('A felhasználó nem található.');
        }

        $form = $this->createForm(UserFormType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isValid()) {
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->addFlash('notice', 'Sikeres rögzítés.');
            } else {
                $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
            }
        }

        $reservations = $this->entityManager->getRepository(Reservation::class)->findAllByUser($entity);
        $leftBehindAssurance = $this->entityManager->getRepository(ReservationItem::class)->findWithLeftBehindAssurance($entity);
        $receipts = $this->entityManager->getRepository(Receipt::class)->findAllByUser($entity);

        return $this->render('portal/profil/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
            'leftBehindAssurance' => $leftBehindAssurance,
            'receipts' => $receipts,
            '_locale' => $_locale,
            'reservations' => $reservations
        ]);
    }
}
