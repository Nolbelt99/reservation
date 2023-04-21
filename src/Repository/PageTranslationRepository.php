<?php

namespace App\Repository;

use App\Entity\PageTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageTranslation>
 *
 * @method PageTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageTranslation[]    findAll()
 * @method PageTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageTranslation::class);
    }
    
    public function findSlug($content, $object)
    {
        $result = $this->createQueryBuilder('t')
            ->andWhere('t.field = :field')
            ->andWhere('t.content = :content')
            ->setParameter('field', 'slug')
            ->setParameter('content', $content)
            ;

        if ($object != null) {
            $result->andWhere('t.object != :object')->setParameter('object', $object);
        }
        return $result->getQuery()->getResult();
    }

    public function findUnneccessaryTranslations()
    {
        $result = $this->createQueryBuilder('t')
            ->andWhere('t.locale = :locale')
            ->setParameter('locale', 'en_US')
            ->getQuery()
            ->getResult()
            ;
        return $result;
    }

    public function findOneBySlug($slug)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.field = :field')
            ->andWhere('t.content = :content')
            ->setParameter('field', 'slug')
            ->setParameter('content', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
