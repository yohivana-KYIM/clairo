<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonAccessStepSixFormConstraint extends Constraint
{
    public string $messageAcceptTerms = "Vous devez accepter les conditions générales.";
    public string $messageSignature = "La signature est obligatoire pour confirmer la véracité des informations.";
}