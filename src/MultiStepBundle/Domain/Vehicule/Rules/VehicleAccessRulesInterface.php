<?php

namespace App\MultiStepBundle\Domain\Vehicule\Rules;

interface VehicleAccessRulesInterface
{
    public function getUselessFields(array $currentData): array;

}