<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReservationCondition extends Constraint
{
    public $message = 'Adjon meg szolgáltatásokat, melyekhez köthető a foglalás!';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}


