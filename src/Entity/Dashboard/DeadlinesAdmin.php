<?php
// ================================================
// FILE: src/Entity/Dashboard/DeadlinesAdmin.php
// ================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_deadlines_admin')]
class DeadlinesAdmin
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'employee_email', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeEmail = null;

    #[ORM\Column(name: 'status', type: TYPES::STRING, length: 255)]
    private string $status;

    #[ORM\Column(name: 'contract_end_date', type: TYPES::TEXT, nullable: true)]
    private ?string $contractEndDate = null;

    #[ORM\Column(name: 'fluxel_training_date', type: TYPES::TEXT, nullable: true)]
    private ?string $fluxelTrainingDate = null;

    #[ORM\Column(name: 'contract_expired_by', type: TYPES::INTEGER)]
    private ?int $contractExpiredBy = 0;

    #[ORM\Column(name: 'training_expired_by', type: TYPES::INTEGER)]
    private ?int $trainingExpiredBy = 0;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getStatus(): string { return $this->status ?? ''; }
    public function getContractEndDate(): ?string {
		 return $this->contractEndDate;
	}
    public function getFluxelTrainingDate(): ?string {
		 return $this->fluxelTrainingDate;
	}
    public function getContractExpiredBy(): int { return $this->contractExpiredBy ?? 0; }
    public function getTrainingExpiredBy(): int { return $this->trainingExpiredBy ?? 0; }

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }
}
