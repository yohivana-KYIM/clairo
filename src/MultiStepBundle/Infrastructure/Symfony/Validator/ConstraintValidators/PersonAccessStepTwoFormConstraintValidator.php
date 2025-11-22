<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\ConstraintValidators;

use App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints\PersonAccessStepTwoFormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonAccessStepTwoFormConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PersonAccessStepTwoFormConstraint);
        if (!empty($value['employee_phone']) && !preg_match('/^0[67][0-9]{8}$/', $value['employee_phone'])) {
            $this->context->buildViolation($constraint->messagePhone)->atPath('employee_phone')->addViolation();
        }

        if (empty($value['employee_email']) || !filter_var($value['employee_email'], FILTER_VALIDATE_EMAIL)) {
            $this->context->buildViolation($constraint->messageEmail)->atPath('employee_email')->addViolation();
        }

        if (empty($value['gender'])) {
            $this->context->buildViolation($constraint->messageGender)->atPath('gender')->addViolation();
        }

        if (empty($value['employee_birthdate'])) {
            $this->context->buildViolation($constraint->messageBirthdate)->atPath('employee_birthdate')->addViolation();
        }

        if (in_array(strtolower($value['employee_birthplace']), ['paris', 'lyon', 'marseille']) && empty($value['employee_birth_district'])) {
            $this->context->buildViolation($constraint->messageDistrict)->atPath('employee_birth_district')->addViolation();
        }

        if ($value['contract_type'] === 'cdd' && empty($value['contract_end_date'])) {
            $this->context->buildViolation($constraint->messageContractEnd)->atPath('contract_end_date')->addViolation();
        }
    }
}