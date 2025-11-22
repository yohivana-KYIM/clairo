<?php

namespace App\MultiStepBundle\Domain\Vehicule\Rules;

use App\MultiStepBundle\Application\VehicleAccessWorkflowService;

class VehicleAccessRulesStepOne implements VehicleAccessRulesInterface
{
    public function __construct(private readonly VehicleAccessWorkflowService $workflowService)
    {
    }

    public function getUselessFields(array $currentData): array
    {
        $previousData = $this->workflowService->getAllData();
        return [];
    }
}