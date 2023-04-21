<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Service;
use App\Enum\CardTypeEnum;
use App\Entity\Reservation;
use App\Entity\ApartmentGuest;
use App\Entity\ReservationItem;
use App\DataFixtures\UserFixtures;
use App\Enum\ReservationStatusEnum;
use App\DataFixtures\ServiceFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReservationFixtures extends Fixture implements DependentFixtureInterface

{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $services = $manager->getRepository(Service::class)->findAll();

        $reservation1 = new Reservation();
        $reservation1->setUser($users[0]);
        $reservation1->setLocale('hu');
        $reservation1->setReservationStatus(ReservationStatusEnum::UNDER_RESERVATION);
        $reservation1->setSumPrice(87000);
        $reservation1->setCreatedAt(new DateTime('-5 days'));
        $reservation1->setReservationNumber((new DateTime())->format('ymd') . '/' . substr(md5(rand()), 0, 6));
        $manager->persist($reservation1);

        $reservationItem1 = new ReservationItem();
        $reservationItem1->setReservation($reservation1);
        $reservationItem1->setStartDate(new DateTime('2023-07-15'));
        $reservationItem1->setEndDate(new DateTime('2023-07-17'));
        $reservationItem1->setService($services[0]);
        $reservationItem1->setReservationPaidSuccesfully(false);
        $reservationItem1->setReservationPrice(60000);
        $manager->persist($reservationItem1);

        $apartmentGuest = new ApartmentGuest();
        $apartmentGuest->setFirstName('Adminné');
        $apartmentGuest->setLastName('Admina');
        $apartmentGuest->setBirthDay(new DateTime('1989-05-01'));
        $apartmentGuest->setCardNumber('123456789');
        $apartmentGuest->setCardType(CardTypeEnum::ID_CARD);
        $apartmentGuest->setReservationItem($reservationItem1);
        $manager->persist($apartmentGuest);

        $apartmentGuest = new ApartmentGuest();
        $apartmentGuest->setFirstName('Admin');
        $apartmentGuest->setLastName('Admin');
        $apartmentGuest->setBirthDay(new DateTime('1989-01-01'));
        $apartmentGuest->setCardNumber('1234567');
        $apartmentGuest->setCardType(CardTypeEnum::DRIVER_LICENSE);
        $apartmentGuest->setReservationItem($reservationItem1);
        $manager->persist($apartmentGuest);

        $reservationItem = new ReservationItem();
        $reservationItem->setReservation($reservation1);
        $reservationItem->setStartDate(new DateTime('2023-07-16'));
        $reservationItem->setEndDate(new DateTime('2023-07-16'));
        $reservationItem->setService($services[3]);
        $reservationItem->setReservationPaidSuccesfully(false);
        $reservationItem->setReservationPrice(1000);
        $manager->persist($reservationItem);

        $reservationItem = new ReservationItem();
        $reservationItem->setReservation($reservation1);
        $reservationItem->setStartDate(new DateTime('2023-07-16'));
        $reservationItem->setEndDate(new DateTime('2023-07-16'));
        $reservationItem->setService($services[4]);
        $reservationItem->setReservationPaidSuccesfully(false);
        $reservationItem->setReservationPrice(1000);
        $manager->persist($reservationItem);

        $reservationItem = new ReservationItem();
        $reservationItem->setReservation($reservation1);
        $reservationItem->setStartDate(new DateTime('2023-07-15'));
        $reservationItem->setEndDate(new DateTime('2023-07-15'));
        $reservationItem->setService($services[6]);
        $reservationItem->setWithCaptain(true);
        $reservationItem->setReservationPaidSuccesfully(false);
        $reservationItem->setAssurancePaidSuccesfully(false);
        $reservationItem->setReservationPrice(35000);
        $reservationItem->setPaidAssurance(10000);
        $reservationItem->isAssurancePaidSuccesfully(true);
        $manager->persist($reservationItem);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ServiceFixtures::class
        ];
    }
}
