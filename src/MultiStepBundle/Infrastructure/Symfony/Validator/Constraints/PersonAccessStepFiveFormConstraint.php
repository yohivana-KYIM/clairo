<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonAccessStepFiveFormConstraint extends Constraint
{
    public string $messageDocuments = "Certains documents obligatoires sont manquants selon votre nationalité.";
    public string $messageTaxiCard = "Carte professionnelle obligatoire pour les sociétés de taxi.";
}