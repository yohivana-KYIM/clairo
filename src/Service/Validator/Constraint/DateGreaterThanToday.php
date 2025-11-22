<?php

namespace App\Service\Validator\Constraint;

use App\Service\Validator\ConstraintValidator\DateGreaterThanTodayValidator;
use Symfony\Component\Validator\Constraint;

class DateGreaterThanToday extends Constraint
{
    public string $message = 'La date doit être supérieure à {{ days }} jours à partir d\'aujourd\'hui.';

    public function validatedBy(): string
    {
        return DateGreaterThanTodayValidator::class;
    }
}
