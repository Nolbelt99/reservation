<?php

namespace App\Repository;

use App\Entity\PaymentItem;
use App\Entity\Transaction;
use App\Enum\TransactionTypeEnum;
use App\Enum\TransactionStatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findListElements()
    {
        return $this->createQueryBuilder('t');
    }

    public function findOneNotFailedByTransaction($transactionId)
    {
        $qb = $this->createQueryBuilder('t');
        $results = $qb
            ->andWhere($qb->expr()->not('t.status = :failed'))
            ->andWhere('t.transactionId = :transactionId')
            ->setParameters([
                'failed'  => TransactionStatusEnum::FAILED,
                'transactionId'  => $transactionId
                ])
            ->getQuery()
            ->getOneOrNullResult()
            ;

        return $results;
    }

    public function findNotPaidByItem(PaymentItem $paymentItem)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->andWhere('t.paymentItem = :paymentItem')
            ->andWhere('t.status = :status')
            ->setParameter('paymentItem', $paymentItem)
            ->setParameter('status', TransactionStatusEnum::UNPAID)
            ->getQuery()
            ->getOneOrNullResult();

        return $queryBuilder;
    }

    public function findAllSuccesfulWithoutReceipt()
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->andWhere('t.type = :type')
            ->andWhere('t.hasReceipt = false')
            ->setParameter('status', TransactionStatusEnum::IPNCHECKED)
            ->setParameter('type', TransactionTypeEnum::PAYMENT)
            ->getQuery()
            ->getResult()
        ;

        return $queryBuilder;
    }
}
