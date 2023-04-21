<?php

namespace App\Repository;

use App\Entity\ApartmentGuest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApartmentGuest>
 *
 * @method ApartmentGuest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApartmentGuest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApartmentGuest[]    findAll()
 * @method ApartmentGuest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApartmentGuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApartmentGuest::class);
    }

}
