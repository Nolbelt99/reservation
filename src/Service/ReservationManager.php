<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Entity\Service;
use App\Enum\ReservationStatusEnum;
use App\Enum\ServiceTypeEnum;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class ReservationManager
{
    private Security $security;
    private SessionInterface $session;
    private Request $request;
    private ReservationRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Security $security,
        RequestStack $requestStack,
        ReservationRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->security = $security;
        $this->session = $requestStack->getSession();
        $this->request = $requestStack->getCurrentRequest();
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }


    public function getReservation(): Reservation
    {
        $reservation = $this->repository->findOneBy([
            'user' => $this->security->getUser(),
            'reservationStatus' => ReservationStatusEnum::UNDER_RESERVATION
        ], ['createdAt' => 'DESC']);

        if (!$reservation && ($reservation = $this->session->get('reservation'))) {
            $unmanagedItems = [];
            foreach ($reservation->getReservationItems() as $unmanagedItem) {
                $unmanagedItems[] = $unmanagedItem;
            }
            foreach ($unmanagedItems as $unmanagedItem) {
                $reservation->removeReservationItem($unmanagedItem);
                $managedItem = clone $unmanagedItem;
                $managedItem->setService(
                    $this->entityManager->getRepository(Service::class)->find($unmanagedItem->getService()->getId())
                );
                $reservation->addReservationItem($managedItem);
            }
        }

        if (!$reservation) {
            $reservation = new Reservation();
            $reservation->setUser($this->security->getUser());
            $reservation->setLocale($this->request->getLocale());
            $reservation->setReservationStatus(ReservationStatusEnum::UNDER_RESERVATION);
            $reservation->setSumPrice(0);
            $reservation->setReservationNumber((new DateTime())->format('ymd') . '/' . substr(md5(rand()), 0, 6));
            $reservation->setCreatedAt(new DateTime());
        }

        return $reservation;
    }

    public function addItem(ReservationItem $reservationItem): Reservation
    {
        $reservation = $this->getReservation();
        $reservation->addReservationItem($reservationItem);
        $reservationLength = $reservationItem->getStartDate()->diff($reservationItem->getEndDate(), true);
        $daysToReserve = $reservationLength->format('%d');
        // $halfDaysToReserve = $reservationLength->format('%h'); //todo
        $service = $reservationItem->getService();
        $price = 0;

        switch ($service->getServiceType()) {
            case ServiceTypeEnum::SHIP:
                $captainPrice = $reservationItem->isWithCaptain() ? $service->getCaptainPrice() : 0;
                $dailyPrice = $service->getPrice() + $captainPrice;
                $price = ($daysToReserve * $dailyPrice) + $service->getCleaningCharge();
                break;
            case ServiceTypeEnum::APARTMENT:
                $price = $daysToReserve * $service->getPrice();
                break;
            case ServiceTypeEnum::EBIKE:
                $price = $daysToReserve * $service->getFullDayPrice();
                // $price += $halfDaysToReserve * $service->getHalfDayPrice();
                break;
        }
        $reservationItem->setReservationPrice($price);
        $reservationItem->setPaidAssurance($service->getAssurance());
        if ($service->getAssurance()) {
            $reservationItem->setAssurancePaidSuccesfully(false);
        }

        $reservation->setSumPrice(($price + $reservation->getSumPrice()));

        return $reservation;
    }
}
