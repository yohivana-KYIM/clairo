<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\ConstraintValidators;

use App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints\PersonAccessStepFiveFormConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PersonAccessStepFiveFormConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof PersonAccessStepFiveFormConstraint);
        $nationality = $value['nationality'] ?? null;
        $isTaxi = isset($value['company_name']) && str_contains(strtolower($value['company_name']), 'taxi');

        if ($isTaxi && empty($value['taxi_card'])) {
            $this->context->buildViolation($constraint->messageTaxiCard)->atPath('taxi_card')->addViolation();
        }

        if ($nationality === 'française') {
            if (empty($value['id_card']) && empty($value['passport'])) {
                $this->context->buildViolation($constraint->messageDocuments)->addViolation();
            }
        } else {
            foreach (['passport', 'residence_permit', 'birth_certificate', 'criminal_record_origin'] as $doc) {
                if (empty($value[$doc])) {
                    $this->context->buildViolation($constraint->messageDocuments)->atPath($doc)->addViolation();
                }
            }

            if (!empty($value['country']) && !empty($value['residence_country']) && $value['country'] !== $value['residence_country']) {
                if (empty($value['criminal_record_nationality'])) {
                    $this->context->buildViolation($constraint->messageDocuments)->atPath('criminal_record_nationality')->addViolation();
                }
            }
        }
    }
}