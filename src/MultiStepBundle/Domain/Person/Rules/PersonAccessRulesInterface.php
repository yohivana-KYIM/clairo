<?php

namespace App\MultiStepBundle\Domain\Person\Rules;

interface PersonAccessRulesInterface
{
    public function getUselessFields(array $currentData): array;

}