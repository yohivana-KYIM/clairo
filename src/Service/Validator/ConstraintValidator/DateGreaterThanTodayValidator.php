<?php

namespace App\Service\Validator\ConstraintValidator;

use App\Service\Validator\Constraint\DateGreaterThanToday;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateGreaterThanTodayValidator extends ConstraintValidator
{
    /**
     * @throws \DateMalformedStringException
     */
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof DateGreaterThanToday);
        if (null === $value || '' === $value) {
            return;
        }

        $today = new DateTime();
        $futureDate = clone $today;
        $futureDate->modify('+15 days');

        if ($value < $futureDate) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ days }}', 15)
                ->addViolation();
        }
    }
}
