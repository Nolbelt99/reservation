<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReservationDate extends Constraint
{
    public $message = 'A(z) {{ service }} már foglalt ebben az időpontban: {{ date }}.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}


