<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\TodoUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoUserRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_todo_user')]
class TodoUser
{

    // 🧩 Synthetic ID added automatically
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

    #[ORM\Column(type: TYPES::STRING, length: 64)]
	private string $status;

    #[ORM\Column(name: 'employee_email', type: TYPES::STRING, length: 255, nullable: true)]
	private ?string $employeeEmail = null;

    #[ORM\Column(name: 'todo_type', type: TYPES::STRING, length: 255)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
	private string $todoType;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getStatus(): string { return $this->status ?? ''; }
    public function getEmployeeEmail(): ?string {
		 return $this->employeeEmail;
	}
    public function getTodoType(): string { return $this->todoType ?? ''; }


    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setEmployeeEmail(?string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
    }

    public function setTodoType(string $todoType): void
    {
        $this->todoType = $todoType;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }
}
