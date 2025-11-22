<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\ConstraintValidators;

use App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints\PersonAccessStepSixFormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonAccessStepSixFormConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PersonAccessStepSixFormConstraint);
        if (empty($value['accept_terms'])) {
            $this->context->buildViolation($constraint->messageAcceptTerms)->atPath('accept_terms')->addViolation();
        }

        if (empty($value['signature'])) {
            $this->context->buildViolation($constraint->messageSignature)->atPath('signature')->addViolation();
        }
    }
}
