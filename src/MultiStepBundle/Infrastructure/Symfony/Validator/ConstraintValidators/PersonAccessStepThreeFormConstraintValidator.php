<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\ConstraintValidators;

use App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints\PersonAccessStepThreeFormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonAccessStepThreeFormConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PersonAccessStepThreeFormConstraint);
        if (isset($value['employment_date'])) {
            $employmentDate = $value['employment_date'];
            foreach (['fluxel_training', 'gies_1', 'gies_2', 'atex_0', 'zar', 'health'] as $key) {
                if (isset($value[$key]) && $value[$key] < $employmentDate) {
                    $this->context->buildViolation($constraint->messageTrainingDate)->atPath($key)->addViolation();
                }
            }
        }
    }
}
