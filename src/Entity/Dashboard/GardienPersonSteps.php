<?php
// =======================================================
// FILE: src/Entity/Dashboard/GardienPersonSteps.php
// =======================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_gardien_person_steps')]
class GardienPersonSteps
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

    #[ORM\Column(name: 'user_id', type: TYPES::INTEGER)]
    private ?int $userId = 0;

    #[ORM\Column(name: 'cesar_step_id', type: TYPES::STRING, length: 255)]
    private string $cesarStepId;

    #[ORM\Column(name: 'security_officer_email', type: TYPES::TEXT, nullable: true)]
    private ?string $securityOfficerEmail = null;

    #[ORM\Column(name: 'siret_data', type: TYPES::TEXT, nullable: true)]
    private ?string $siretData = null;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

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
    public function getUserId(): int { return $this->userId ?? 0; }
    public function getCesarStepId(): string { return $this->cesarStepId ?? ''; }
    public function getSecurityOfficerEmail(): ?string {
		 return $this->securityOfficerEmail;
	}
    public function getSiretData(): ?string {
		 return $this->siretData;
	}
    public function getCompanyName(): ?string {
		 return $this->companyName;
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

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setCesarStepId(string $cesarStepId): void
    {
        $this->cesarStepId = $cesarStepId;
    }
}
