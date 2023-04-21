<?php

namespace App\Validator;

use App\Enum\ReservationTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ReservationConditionValidator extends ConstraintValidator
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof ReservationCondition) {
            throw new UnexpectedTypeException($constraint, ReservationCondition::class);
        }

        if (null === $entity || '' === $entity) {
            return;
        }

        if ($entity->getReservationType() == ReservationTypeEnum::NOT_INDEPENDENT && empty($entity->getConditionServices()->getValues())) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
