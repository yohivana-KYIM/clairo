<?php

namespace App\Service\Validator\ConstraintValidator;

use App\Service\Validator\Classes\CompositePassValidator;
use App\Service\Validator\Constraint\PasswordConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for password constraints using composite strategies.
 */
class PasswordConstraintValidator extends ConstraintValidator
{
    private readonly CompositePassValidator $compositeValidator;

    public function __construct(CompositePassValidator $compositeValidator)
    {
        $this->compositeValidator = $compositeValidator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($constraint instanceof PasswordConstraint) {
            if (!$this->compositeValidator->validate($value)) {
                $this->context->buildViolation($constraint->message)->addViolation();
                foreach ($this->compositeValidator->getEncounteredErrors() as $error) {
                    $this->context->buildViolation($error)->addViolation();
                }
            }
        }
    }
}