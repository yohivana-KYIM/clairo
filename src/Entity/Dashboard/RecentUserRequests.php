<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RecentUserRequestsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecentUserRequestsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_recent_user_requests')]
class RecentUserRequests
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

    #[ORM\Column(type: TYPES::STRING, length: 64)]
	private string $status;

    #[ORM\Column(name: 'request_date', type: TYPES::STRING, length: 10, nullable: true)]
	private ?string $requestDate = null;
    #[ORM\Column(name: 'days_since', type: TYPES::INTEGER, nullable: true)]
	private ?int $daysSince = null;
    #[ORM\Column(type: TYPES::STRING, length: 255)]
	private string $priority;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getStatus(): string { return $this->status ?? ''; }
    public function getRequestDate(): ?string {
		 return $this->requestDate;
	}
    public function getDaysSince(): ?int {
		 return $this->daysSince;
	}
    public function getPriority(): string { return $this->priority ?? ''; }

    public function setStepId(int $stepId): self
    {
        $this->stepId = $stepId;

        return $this;
    }

    public function setEmployeeEmail(?string $employeeEmail): self
    {
        $this->employeeEmail = $employeeEmail;

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setRequestDate(?string $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function setDaysSince(?int $daysSince): self
    {
        $this->daysSince = $daysSince;

        return $this;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
