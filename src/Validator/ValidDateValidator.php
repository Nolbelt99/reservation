<?php

namespace App\Validator\Constraints;

use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidDate) {
            throw new UnexpectedTypeException($constraint, ValidDate::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!is_null($value)) {

            try {
                $date = new DateTime($value);
            } catch (\Exception $e) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
