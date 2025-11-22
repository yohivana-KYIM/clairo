<?php

namespace App\MultiStepBundle\Domain\Person\Rules;

use App\MultiStepBundle\Application\PersonAccessWorkflowService;

class PersonAccessRulesStepTwo implements PersonAccessRulesInterface
{
    public function __construct(private readonly PersonAccessWorkflowService $workflowService)
    {
    }

    public function getUselessFields(array $currentData): array
    {
        $previousData = $this->workflowService->getAllData();
        $stepOne = $previousData['person_step_one'] ?? [];

        $accessDuration = strtolower($stepOne['access_duration'] ?? '');

        $useless = [];

        if ($accessDuration === 'temporaire') {
            $useless = [
                "maiden_name",
                "employee_birth_postale_code",
                "employee_birth_district",
                "social_security_number",
                "employee_email",
                "employee_refugee",
                "employee_address_autocomplete",
                "section_employee_address",
                "postal_code",
                "city",
                "resident_situation",
                "father_name",
                "father_first_name",
                "mother_maiden_name",
                "mother_first_name",
                "contract_type",
                "employment_date",
                "contract_end_date",
                "matricule"
            ];
        }

        return $useless;
    }
}