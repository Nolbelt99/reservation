<?php

namespace App\Validator;

use App\Entity\ReservationItem;
use App\Enum\ReservationStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ReservationDateValidator extends ConstraintValidator
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof ReservationDate) {
            throw new UnexpectedTypeException($constraint, ReservationDate::class);
        }

        if (null === $entity || '' === $entity) {
            return;
        }

        $oldEntity = $this->entityManager->getUnitOfWork()->getOriginalEntityData($entity);
        if (array_key_exists('reservationStatus', $oldEntity)) {
            $items = $oldEntity['reservationItems']->getValues();
            if ($oldEntity['reservationStatus'] != $entity->getReservationStatus()) {
                if ($oldEntity['reservationStatus'] == ReservationStatusEnum::DELETED &&
                    ($entity->getReservationStatus() == ReservationStatusEnum::WAITING_FOR_PAYMENT ||
                        $entity->getReservationStatus() == ReservationStatusEnum::PAID_RESERVATION)) {
                    foreach ($items as $item) {
                        $results = $this->entityManager->getRepository(ReservationItem::class)->findServiceWithSameDate($item);
                        if (!empty($results) && count($results) >= $item->getService()->getAvaibleSameTime()) {
                            foreach ($results as $result) {
                                $this->context->buildViolation($constraint->message)
                                    ->setParameter('{{ service }}', $result->getService()->getName())
                                    ->setParameter('{{ date }}', $result->getStartDate()->format('Y.m.d') . ' - ' . $result->getEndDate()->format('Y.m.d'))
                                    ->addViolation();
                            }
                        }
                    }
                }
           }
        } else {
            $items = $entity->getReservationItems();
            foreach ($items as $item) {
                $results = $this->entityManager->getRepository(ReservationItem::class)->findServiceWithSameDate($item);
                if (!empty($results) && count($results) >= $item->getService()->getAvaibleSameTime()) {
                    foreach ($results as $result) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ service }}', $result->getService()->getName())
                            ->setParameter('{{ date }}', $result->getStartDate()->format('Y.m.d') . ' - ' . $result->getEndDate()->format('Y.m.d'))
                            ->addViolation();
                    }
                }
            }
        }
    }
}
