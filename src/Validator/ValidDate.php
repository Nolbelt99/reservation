<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidDate extends Constraint
{
    public $message = 'Érvénytelen dátumformátum.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}


