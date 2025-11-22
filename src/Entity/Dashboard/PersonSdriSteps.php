<?php
// ==================================================
// FILE: src/Entity/Dashboard/PersonSdriSteps.php
// ==================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_person_sdri_steps')]
class PersonSdriSteps
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'step_number', type: TYPES::STRING, length: 255)]
    private string $stepNumber;

    #[ORM\Column(name: 'step_type', type: TYPES::STRING, length: 255)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
    private string $stepType;

    #[ORM\Column(name: 'status', type: TYPES::STRING, length: 255)]
    private string $status;

    #[ORM\Column(name: 'cesar_step_id', type: TYPES::STRING, length: 255)]
    private string $cesarStepId;

    #[ORM\Column(name: 'user_id', type: TYPES::INTEGER)]
    private ?int $userId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'siren', type: TYPES::TEXT, nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(name: 'employee_first_name', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeFirstName = null;

    #[ORM\Column(name: 'employee_last_name', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeLastName = null;

    #[ORM\Column(name: 'request_date', type: TYPES::TEXT, nullable: true)]
    private ?string $requestDate = null;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getStepNumber(): string { return $this->stepNumber ?? ''; }
    public function getStepType(): string { return $this->stepType ?? ''; }
    public function getStatus(): string { return $this->status ?? ''; }
    public function getCesarStepId(): string { return $this->cesarStepId ?? ''; }
    public function getUserId(): int { return $this->userId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getSiren(): ?string {
		 return $this->siren;
	}
    public function getEmployeeFirstName(): ?string {
		 return $this->employeeFirstName;
	}
    public function getEmployeeLastName(): ?string {
		 return $this->employeeLastName;
	}
    public function getRequestDate(): ?string {
		 return $this->requestDate;
	}

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setStepNumber(string $stepNumber): void
    {
        $this->stepNumber = $stepNumber;
    }

    public function setStepType(string $stepType): void
    {
        $this->stepType = $stepType;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setCesarStepId(string $cesarStepId): void
    {
        $this->cesarStepId = $cesarStepId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
