<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonAccessStepThreeFormConstraint extends Constraint
{
    public string $messageTrainingDate = "Les dates de formation doivent être postérieures à la date d'embauche.";
}