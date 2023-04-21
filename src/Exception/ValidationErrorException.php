<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorException extends \Exception
{
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        parent::__construct('Validation failed.');
    }

    public function getMessages()
    {
        $messages = [];

        foreach ($this->violations as $constraint) {
            $prop = $constraint->getPropertyPath();
            $messages[$prop] = $constraint->getMessage();
        }

        return $messages;
    }
}
