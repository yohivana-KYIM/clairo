<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\AlertsUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlertsUserRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_alerts_user')]
class AlertsUser
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 64)]
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

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'employee_email', type: TYPES::STRING, length: 255, nullable: true)]
	private ?string $employeeEmail = null;

    #[ORM\Column(type: TYPES::STRING, length: 64)]
	private string $status;
    #[ORM\Column(name: 'request_date', type: TYPES::STRING, length: 10, nullable: true)]
	private ?string $requestDate = null; // 'YYYY-MM-DD'

    #[ORM\Column(name: 'alert_type', type: TYPES::STRING, length: 255)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
	private string $alertType;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getStatus(): string { return $this->status ?? ''; }
    public function getRequestDate(): ?string {
		 return $this->requestDate;
	}
    public function getAlertType(): string { return $this->alertType ?? ''; }


    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setEmployeeEmail(?string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setRequestDate(?string $requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    public function setAlertType(string $alertType): void
    {
        $this->alertType = $alertType;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
