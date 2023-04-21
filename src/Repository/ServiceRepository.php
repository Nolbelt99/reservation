<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function findListElements($_locale, $op)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->andWhere('t.deleted = false')
            ->andWhere('t.serviceType = :op')
            ->setParameter('op', $op)
        ;
        $queryBuilder->getQuery()->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $queryBuilder->getQuery()->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $_locale
        );
        return $queryBuilder;
    }

    public function findForHomepage($_locale, $op) {
        return $this->findListElements($_locale, $op)
            ->getQuery()
            ->getResult();
    }

    public function findBySlug($_locale, $op, $slug): Service {
        return $this->findListElements($_locale, $op)
            ->leftJoin('t.translations', 'tr')
            ->andWhere('tr.field = :field')
            ->andWhere('tr.content = :content')
            ->setParameter('field', 'slug')
            ->setParameter('content', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
