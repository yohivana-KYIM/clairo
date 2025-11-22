<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonAccessStepOneFormConstraint extends Constraint
{
    public string $messageRequestDate = "La date de la demande ne peut pas être antérieure à aujourd'hui.";
}