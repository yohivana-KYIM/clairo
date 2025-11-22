<?php
// ===============================================
// FILE: src/Entity/Dashboard/DeadlinesSdri.php
// ===============================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_deadlines_sdri')]
class DeadlinesSdri
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'fluxel_training_date', type: TYPES::TEXT, nullable: true)]
    private ?string $fluxelTrainingDate = null;

    #[ORM\Column(name: 'employee_email', type: TYPES::TEXT, nullable: true)]
    private ?string $employeeEmail = null;

    #[ORM\Column(name: 'days_left_training', type: TYPES::INTEGER)]
    private ?int $daysLeftTraining = 0;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getFluxelTrainingDate(): ?string {
		 return $this->fluxelTrainingDate;
	}
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getDaysLeftTraining(): int { return $this->daysLeftTraining ?? 0; }
}
