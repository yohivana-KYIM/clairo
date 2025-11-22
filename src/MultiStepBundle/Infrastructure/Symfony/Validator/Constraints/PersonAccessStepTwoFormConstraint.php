<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonAccessStepTwoFormConstraint extends Constraint
{
    public string $messagePhone = 'Le numéro de téléphone doit être valide et commencer par 06 ou 07.';
    public string $messageEmail = 'Adresse email non valide.';
    public string $messageGender = 'Le genre est obligatoire.';
    public string $messageBirthdate = 'La date de naissance est obligatoire.';
    public string $messageDistrict = 'L\'arrondissement est obligatoire pour Paris, Lyon ou Marseille.';
    public string $messageContractEnd = 'Veuillez indiquer la date de fin du contrat.';
}
