<?php
// =============================================
// FILE: src/Entity/Dashboard/TodoAdmin.php
// =============================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_todo_admin')]
class TodoAdmin
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'status', type: TYPES::STRING, length: 255)]
    private string $status;

    #[ORM\Column(name: 'request_date', type: TYPES::TEXT, nullable: true)]
    private ?string $requestDate = null;

    #[ORM\Column(name: 'todo_type', type: TYPES::STRING, length: 255)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
    private string $todoType;

    #[ORM\Column(name: 'priority_level', type: TYPES::STRING, length: 32)]
    private string $priorityLevel;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getStatus(): string { return $this->status ?? ''; }
    public function getRequestDate(): ?string {
		 return $this->requestDate;
	}
    public function getTodoType(): string { return $this->todoType ?? ''; }
    public function getPriorityLevel(): string { return $this->priorityLevel ?? ''; }

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setRequestDate(?string $requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    public function setTodoType(string $todoType): void
    {
        $this->todoType = $todoType;
    }

    public function setPriorityLevel(string $priorityLevel): void
    {
        $this->priorityLevel = $priorityLevel;
    }
}
