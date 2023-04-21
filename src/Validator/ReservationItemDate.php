<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReservationItemDate extends Constraint
{
    public $message = '{{ message }}';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}


