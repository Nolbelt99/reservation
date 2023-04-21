<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }
    
    public function findListElements($_locale)
    {
        $queryBuilder = $this->createQueryBuilder('t');
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

    public function findBySlug($_locale, $slug)
    {
        return $this->findListElements($_locale)
            ->leftJoin('t.translations', 'tr')
            ->andWhere('tr.field = :field')
            ->andWhere('tr.content = :content')
            ->setParameter('field', 'slug')
            ->setParameter('content', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}
