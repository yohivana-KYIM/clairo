<?php

namespace App\MultiStepBundle\Domain\Person\Rules;

use App\MultiStepBundle\Application\PersonAccessWorkflowService;

class PersonAccessRulesStepThree implements PersonAccessRulesInterface
{
    public function __construct(private readonly PersonAccessWorkflowService $workflowService)
    {
    }

    public function getUselessFields(array $currentData): array
    {
        $previousData = $this->workflowService->getAllData();
        return [];
    }
}