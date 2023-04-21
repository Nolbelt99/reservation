<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Reservation;
use App\Enum\TransactionTypeEnum;
use App\Enum\ReservationStatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findListElements(string $status)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'user')
            ->orderBy('r.createdAt', 'desc')
        ;

        if ($status) {
            $queryBuilder = $this->createQueryBuilder('r')
                ->andWhere('r.reservationStatus = :status')
                ->setParameter('status', ReservationStatusEnum::MANUAL_RESERVAITON)
            ;
        } else {
            $queryBuilder = $this->createQueryBuilder('r')
                ->andWhere('r.reservationStatus != :status')
                ->setParameter('status', ReservationStatusEnum::MANUAL_RESERVAITON)
            ;
        }

        return $queryBuilder;
    }

    public function findOneById(int $id, $user, string $status)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->andWhere('r.id = :id')
            ->andWhere('r.user = :user')
            ->andWhere('r.reservationStatus = :status')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->getQuery()
            ->getOneOrNullResult();

        return $queryBuilder;
    }

    public function findAllByUser($user)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.reservationStatus != :status')
            ->setParameter('user', $user)
            ->setParameter('status', ReservationStatusEnum::DELETED)
            ->getQuery()
            ->getResult();

        return $queryBuilder;
    }

}
