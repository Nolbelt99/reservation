<?php

namespace App\Repository;

use App\Entity\Blog;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 *
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findListElements($admin)
    {
        if ($admin === true) {
            $queryBuilder = $this->createQueryBuilder('b');
        } else {
            $queryBuilder = $this->createQueryBuilder('b')
                ->andWhere('b.publishedAt < :today')
                ->setParameter('today', new DateTime())
                ->getQuery()
                ->getResult()
            ;
        }

        return $queryBuilder;
    }
}
