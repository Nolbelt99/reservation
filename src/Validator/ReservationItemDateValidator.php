<?php

namespace App\Validator;

use App\Entity\ReservationItem;
use App\Enum\ReservationStatusEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ReservationItemDateValidator extends ConstraintValidator
{
    protected EntityManagerInterface $entityManager;
    protected TranslatableListener $translatableListener;
    protected ParameterBagInterface $param;

    public function __construct(EntityManagerInterface $entityManager, TranslatableListener $translatableListener, ParameterBagInterface $param)
    {
        $this->entityManager = $entityManager;
        $this->translatableListener = $translatableListener;
        $this->param = $param;
        $this->translatableListener->setTranslatableLocale($this->param->get('locale'));
    }

    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof ReservationItemDate) {
            throw new UnexpectedTypeException($constraint, ReservationItemDate::class);
        }

        if (null === $entity || '' === $entity) {
            return;
        }

        if (!$entity->getReservation()) {
            $results = $this->entityManager->getRepository(ReservationItem::class)->findServiceWithSameDate($entity);
            if (!empty($results) && count($results) >= $entity->getService()->getAvaibleSameTime()) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ message }}', 'A szolgáltatás az adott időszakban nem elérhető.')
                        ->addViolation();
            }
            
            if (date_diff($entity->getEndDate(), $entity->getStartDate())->d + 1 < $entity->getService()->getMinDay()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ message }}', 'A szolgáltatást legalább ' .  $entity->getService()->getMinDay() . ' napra kell lefoglalni.')
                    ->addViolation();
            }
        }

        if ($entity->getEndDate() < $entity->getStartDate()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ message }}', 'A zárásnak később kell lennie, mint a kezdésnek.')
                ->addViolation();
        }
        
        if ($entity->getStartDate() < new DateTime('-1 day')) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ message }}', 'Nem foglalhat korábbi dátummal.')
                ->addViolation();
        }
    }
}
