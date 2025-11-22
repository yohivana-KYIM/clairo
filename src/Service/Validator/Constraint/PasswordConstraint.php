<?php

namespace App\Service\Validator\Constraint;

use App\Service\Validator\ConstraintValidator\PasswordConstraintValidator;
use Symfony\Component\Validator\Constraint;

class PasswordConstraint extends Constraint
{
    public string $message = 'Le mot de passe saisie ne respecte pas notre politique de confidentialité';

    public function validatedBy(): string
    {
        return PasswordConstraintValidator::class;
    }
}