<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Enum\CaptainTypeEnum;
use App\Entity\ReservationItem;
use App\Form\ReservationItemType;
use App\Service\ReservationManager;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/{_locale}", requirements={"_locale": "hu|en|de|"}, name="portal_page_service_")
 */
class ServiceController extends BaseController
{
    /**
     * @Route("/{serviceType}", name="list")
     */
    public function list(
        ServiceRepository $repository,
        TranslatableListener $translatableListener,
        Request $request,
        string $serviceType
    ): Response {
        $_locale = $request->getLocale();

        $translatableListener->setTranslatableLocale($_locale);
        $entities = $repository->findForHomepage($_locale, strtoupper($serviceType));

        return $this->render('portal/service/list.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/{serviceType}/{slug}", name="show")
     */
    public function show(
        string $slug,
        ServiceRepository $repository,
        TranslatableListener $translatableListener,
        Request $request,
        SessionInterface $session,
        ReservationManager $manager,
        EntityManagerInterface $entityManager,
        string $serviceType
    ): Response {
        $_locale = $request->getLocale();
        $translatableListener->setTranslatableLocale($_locale);
        $entity = $repository->findBySlug($_locale, strtoupper($serviceType), $slug);
        $entities = $repository->findForHomepage($_locale, strtoupper($serviceType));

        $reservationItems = $entityManager->getRepository(ReservationItem::class)->findUnavaibleDates($entity);
        $bookedDates = [];
        $grouppedDates = [];
        $unavaibleDates = [];
        $checkedIds = [];
        foreach ($reservationItems as $item) {
            $results = $entityManager->getRepository(ReservationItem::class)->findServiceWithSameDate($item);
            if (!empty($results) && count($results) >= $entity->getAvaibleSameTime()) {
                foreach ($results as $key => $result) {
                    $endDate = new DateTime($result->getEndDate()->format('Y-m-d'));
                    $period = new DatePeriod(
                        $result->getStartDate(),
                        new DateInterval('P1D'),
                        $endDate->modify('+1 day')
                   );

                   foreach ($period as $value) {
                        if (!in_array($result->getId(), $checkedIds)) {
                            $bookedDates[] = $value->format('Y-m-d');
                        }
                    }

                    $checkedIds[] = $result->getId();
                }
            }
        }

        foreach ( $bookedDates as $value ) {
            $grouppedDates[$value][] = $value;
        }

        foreach ($grouppedDates as $key => $grouppedDate) {
            if (count($grouppedDate) >=  $entity->getAvaibleSameTime()) {
                $unavaibleDates[] = $key;
            }
        }

        $reservationItem = new ReservationItem();
        $form = $this->createForm(ReservationItemType::class, $reservationItem, ['service' => $entity]);
        $reservationItem->setService($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
/*             if ($reservationItem->getStartDate()->diff($reservationItem->getEndDate(), true)->format('%d') < $entity->getMinDay()) {
                // itt kevesebb a foglalt napok száma, mint a mininimum
                return $this->render('portal/service/show.html.twig', [
                    'entity' => $entity,
                    'entities' => $entities,
                    'form' => $form->createView(),
                ]);
            } */
            $reservationItem->setStartDate(new DateTime($form->get('startDate')->getData()));
            $reservationItem->setEndDate( new DateTime($form->get('endDate')->getData()));

            if ($entity->getCaptainType() == CaptainTypeEnum::MANDATORY) {
                $reservationItem->setWithCaptain(true);
            }
            $reservation = $manager->addItem($reservationItem);
            if ($user = $this->getUser()) {
                $reservation->setUser($user);
                $entityManager->persist($reservation);
                $entityManager->flush();            
                $session->clear();
            } else {
                $session->set('reservation', $reservation);
            }
            
            return $this->redirectToRoute('portal_page_booking');
        }

        return $this->render('portal/service/show.html.twig', [
            'entity' => $entity,
            'unavaibleDates' => $unavaibleDates,
            'entities' => $entities,
            'form' => $form->createView(),
        ]);
    }
}
