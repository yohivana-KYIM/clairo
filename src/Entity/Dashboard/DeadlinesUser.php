<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\DeadlinesUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeadlinesUserRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_deadlines_user')]
class DeadlinesUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $id;

    public function __construct()
    {
        // Generate synthetic ID on construction
        $this->computeSyntheticId();
    }

    private function computeSyntheticId(): void
    {
        // Build an MD5 hash of all object vars
        $this->id = md5(json_encode(get_object_vars($this)));
    }

    public function getId(): string { return $this->id ?? ''; }

    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
	private ?int $stepId = 0;

    #[ORM\Column(name: 'employee_email', type: TYPES::STRING, length: 255, nullable: true)]
	private ?string $employeeEmail = null;

    #[ORM\Column(name: 'contract_end_date', type: TYPES::STRING, length: 10, nullable: true)]
	private ?string $contractEndDate = null;
    #[ORM\Column(name: 'fluxel_training_date', type: TYPES::STRING, length: 10, nullable: true)]
	private ?string $fluxelTrainingDate = null;

    #[ORM\Column(name: 'days_until_contract_end', type: TYPES::INTEGER, nullable: true)]
	private ?int $daysUntilContractEnd = null;
    #[ORM\Column(name: 'days_until_training_expire', type: TYPES::INTEGER, nullable: true)]
	private ?int $daysUntilTrainingExpire = null;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getContractEndDate(): ?string {
		 return $this->contractEndDate;
	}
    public function getFluxelTrainingDate(): ?string {
		 return $this->fluxelTrainingDate;
	}
    public function getDaysUntilContractEnd(): ?int {
		 return $this->daysUntilContractEnd;
	}
    public function getDaysUntilTrainingExpire(): ?int {
		 return $this->daysUntilTrainingExpire;
	}


    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setEmployeeEmail(?string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
    }

    public function setContractEndDate(?string $contractEndDate): void
    {
        $this->contractEndDate = $contractEndDate;
    }

    public function setFluxelTrainingDate(?string $fluxelTrainingDate): void
    {
        $this->fluxelTrainingDate = $fluxelTrainingDate;
    }

    public function setDaysUntilContractEnd(?int $daysUntilContractEnd): void
    {
        $this->daysUntilContractEnd = $daysUntilContractEnd;
    }

    public function setDaysUntilTrainingExpire(?int $daysUntilTrainingExpire): void
    {
        $this->daysUntilTrainingExpire = $daysUntilTrainingExpire;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
