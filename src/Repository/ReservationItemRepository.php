<?php

namespace App\Repository;

use DateTime;
use App\Entity\Service;
use App\Entity\PaymentItem;
use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Enum\TransactionTypeEnum;
use App\Enum\ReservationStatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ReservationItem>
 *
 * @method ReservationItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationItem[]    findAll()
 * @method ReservationItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationItem::class);
    }
    
    public function findServiceWithSameDate(ReservationItem $item)
    {
        $results = $this->createQueryBuilder('i')
            ->leftJoin('i.reservation', 'r')
            ->andWhere('i.service = :service')
            ->andWhere('(i.startDate BETWEEN :startDate AND :endDate) OR (i.endDate BETWEEN :startDate AND :endDate)')
            ->andWhere('r.reservationStatus != :reservationStatus')
            ->setParameter('service', $item->getService())
            ->setParameter('startDate', $item->getStartDate())
            ->setParameter('endDate', $item->getEndDate())
            ->setParameter('reservationStatus', 'DELETED')
            ->getQuery()
            ->getResult()
        ;

        return $results;
    }

    public function findItemToLock(PaymentItem $item)
    {
        $results = $this->createQueryBuilder('i')
            ->where(':item MEMBER OF i.paymentItems')
            ->andWhere('i.startDate < :startDate')
            ->setParameter("item", $item)
            ->setParameter("startDate", new DateTime('+21 days'))
            ->orderBy('i.startDate', 'asc')
            ->getQuery()
            ->getResult()
        ;

        return $results;
    }

    public function findUnavaibleDates(Service $service) {
        $results = $this->createQueryBuilder('i')
            ->leftJoin('i.reservation', 'r')
            ->andWhere('i.service = :service')
            ->andWhere('i.startDate > :startDate')
            ->andWhere('r.reservationStatus != :reservationStatus')
            ->setParameter('service', $service)
            ->setParameter('startDate', new DateTime('-1 day'))
            ->setParameter('reservationStatus', 'DELETED')
            ->getQuery()
            ->getResult();

        return $results;
    }

    public function findAllByReservationId(int $id)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->leftJoin('i.reservation', 'r')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }

    
    public function findWithLeftBehindAssurance($user)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->leftJoin('i.reservation', 'r')
            ->andWhere('r.user = :user')
            ->andWhere('i.assurancePaidSuccesfully = false')
            ->andWhere('r.reservationStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('status', ReservationStatusEnum::PAID_RESERVATION)
            ->orderBy('i.startDate', 'ASC')
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }

}
