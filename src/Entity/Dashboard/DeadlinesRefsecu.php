<?php
// ==================================================
// FILE: src/Entity/Dashboard/DeadlinesRefsecu.php
// ==================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_deadlines_refsecu')]
class DeadlinesRefsecu
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'employee_first_name', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeFirstName = null;

    #[ORM\Column(name: 'employee_last_name', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeLastName = null;

    #[ORM\Column(name: 'contract_end_date', type: TYPES::TEXT, nullable: true)]
    private ?string $contractEndDate = null;

    #[ORM\Column(name: 'access_duration_step6', type: TYPES::TEXT, nullable: true)]
    private ?string $accessDurationStep6 = null;

    #[ORM\Column(name: 'days_left_contract', type: TYPES::INTEGER, nullable: true)]
    private ?int $daysLeftContract = 0;

    #[ORM\Column(name: 'days_left_access', type: TYPES::INTEGER)]
    private ?int $daysLeftAccess = 0;

    #[ORM\Column(name: 'fluxel_training_date', type: TYPES::TEXT, nullable: true)]
    private ?string $fluxelTrainingDate = null;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getEmployeeFirstName(): ?string {
		 return $this->employeeFirstName;
	}
    public function getEmployeeLastName(): ?string {
		 return $this->employeeLastName;
	}
    public function getContractEndDate(): ?string {
		 return $this->contractEndDate;
	}
    public function getAccessDurationStep6(): ?string {
		 return $this->accessDurationStep6;
	}
    public function getDaysLeftContract(): int {
        return $this->daysLeftContract ?? 0;
    }
    public function getDaysLeftAccess(): int {
        return $this->daysLeftAccess ?? 0;
    }

    public function getFluxelTrainingDate(): ?string
    {
        return $this->fluxelTrainingDate;
    }

    public function setFluxelTrainingDate(?string $fluxelTrainingDate): void
    {
        $this->fluxelTrainingDate = $fluxelTrainingDate;
    }

    public function setDaysLeftAccess(int $daysLeftAccess): void
    {
        $this->daysLeftAccess = $daysLeftAccess;
    }

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }
}
