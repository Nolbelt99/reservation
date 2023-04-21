<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Enum\ReservationStatusEnum;
use App\Enum\ServiceTypeEnum;
use App\Repository\ReservationRepository;
use App\Repository\ServiceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class PaymentManager
{
    private Security $security;
    private SessionInterface $session;
    private Request $request;
    private ReservationRepository $repository;

    public function __construct(
        Security $security,
        RequestStack $requestStack,
        ReservationRepository $repository,
    ) {
        $this->security = $security;
        $this->session = $requestStack->getSession();
        $this->request = $requestStack->getCurrentRequest();
        $this->repository = $repository;
    }


    public function getReservation(): Reservation
    {
        $reservation = $this->session->get('reservation');

        if (!$reservation) {
            $this->repository->findOneBy([
                'user' => $this->security->getUser(),
                'reservationStatus' => ReservationStatusEnum::WAITING_FOR_PAYMENT
            ], ['createdAt' => 'DESC']);
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
                $dailyPrice = $service->getPrice() + ($reservationItem->isWithCaptain() ? $service->getCaptainPrice() : 0);
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

        $reservation->setSumPrice($price);

        return $reservation;
    }
}
