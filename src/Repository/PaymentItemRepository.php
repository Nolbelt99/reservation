<?php

namespace App\Repository;

use App\Entity\PaymentItem;
use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Enum\TransactionTypeEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PaymentItem>
 *
 * @method PaymentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentItem[]    findAll()
 * @method PaymentItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentItem::class);
    }

    public function findPaymentItem(Reservation $reservation, string $type, string $companyName)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.reservation = :reservation')
            ->andWhere('p.companyName = :companyName')
            ->andWhere('p.type = :type')
            ->setParameter('reservation', $reservation)
            ->setParameter('companyName', $companyName)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();

        return $queryBuilder;
    }

    public function findByReservationItem(ReservationItem $item, string $type, string $companyName)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere(':item MEMBER OF p.reservationItems')
            ->andWhere('p.companyName = :companyName')
            ->andWhere('p.type = :type')
            ->setParameter('companyName', $companyName)
            ->setParameter('type', $type)
            ->setParameter("item", $item->getId())
            ->getQuery()
            ->getOneOrNullResult();

        return $queryBuilder;
    }

    public function getOrderedItems(Reservation $reservation)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.reservation = :reservation')
            ->andWhere('p.paid = false')
            ->setParameter('reservation', $reservation)
            ->orderBy('p.companyPriority', 'asc')
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }

        
    public function findRemainingPaymentItem(Reservation $reservation, string $type)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->andWhere('r.reservation = :reservation')
            ->andWhere('r.type = :type')
            ->andWhere('r.paid = false')
            ->setParameter('reservation', $reservation)
            ->setParameter('type', $type);

/*         if ($type == TransactionTypeEnum::LOCK) {
            $queryBuilder = $queryBuilder
                ->where(':reservationItemId MEMBER OF c.reservationItems')
                ->setParameter("reservationItemId", $reservationItemId);
        } */

        $queryBuilder = $queryBuilder
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }
}
