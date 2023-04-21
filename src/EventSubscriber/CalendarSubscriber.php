<?php

namespace App\EventSubscriber;

use App\Enum\ServiceTypeEnum;
use CalendarBundle\Entity\Event;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use Gedmo\Translatable\TranslatableListener;
use App\Repository\ReservationItemRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $reservationItemRepository;
    private $param;
    private $translatableListener;
    private $router;

    public function __construct(
        ReservationItemRepository $reservationItemRepository,
        TranslatableListener $translatableListener,
        ParameterBagInterface $param,
        UrlGeneratorInterface $router
    ) {
        $this->reservationItemRepository = $reservationItemRepository;
        $this->router = $router;
        $this->param = $param;
        $this->translatableListener = $translatableListener;
        $this->translatableListener->setTranslatableLocale($this->param->get('locale'));
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $reservations = $this->reservationItemRepository
            ->createQueryBuilder('i')
            ->where('i.startDate BETWEEN :start and :end OR i.endDate BETWEEN :start and :end')
            ->setParameter('start', $calendar->getStart()->format('Y-m-d'))
            ->setParameter('end', $calendar->getEnd()->format('Y-m-d'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($reservations as $reservation) {

            $event = new Event(
                $reservation->getService()->getName(),
                $reservation->getStartDate(),
                $reservation->getEndDate()
            );

            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             */

            switch ($reservation->getService()->getServiceType()) {
                case ServiceTypeEnum::APARTMENT:
                    $event->setOptions([
                        'backgroundColor' => 'red',
                        'borderColor' => 'red',
                    ]);
                    break;
                case ServiceTypeEnum::SHIP:
                    $event->setOptions([
                        'backgroundColor' => 'blue',
                        'borderColor' => 'blue',
                    ]);
                    break;
                case ServiceTypeEnum::EBIKE:
                    $event->setOptions([
                        'backgroundColor' => 'green',
                        'borderColor' => 'green',
                    ]);
                    break;
            }


            $event->addOption(
                'url',
                $this->router->generate('admin_reservation_form', [
                    'id' => $reservation->getReservation()->getId(),
                ])
            );


            $calendar->addEvent($event);
        }
    }
}