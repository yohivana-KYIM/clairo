<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\ConstraintValidators;

use App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints\PersonAccessStepOneFormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonAccessStepOneFormConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PersonAccessStepOneFormConstraint);
        if (!isset($value['request_date'])) return;
        $today = new \DateTime();
        $requestDate = $value['request_date'];
        if ($requestDate < $today) {
            $this->context->buildViolation($constraint->messageRequestDate)
                ->atPath('request_date')
                ->addViolation();
        }
    }
}
