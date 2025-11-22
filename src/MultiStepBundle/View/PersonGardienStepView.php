<?php

namespace App\MultiStepBundle\View;

use App\MultiStepBundle\Entity\StepData;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_gardien_person_steps')]
class PersonGardienStepView
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: Types::INTEGER)]
    private int $stepId;

    #[ORM\OneToOne(targetEntity: StepData::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: "step_id", referencedColumnName: "step_id", nullable: false)]
    private ?StepData $stepData = null;

    #[ORM\Column(name: 'step_number', type: Types::STRING)]
    private string $stepNumber;

    #[ORM\Column(name: 'step_type', type: TYPES::STRING)]
    private string $stepType;

    #[ORM\Column(name: 'status', type: TYPES::STRING)]
    private string $status;

    #[ORM\Column(name: 'user_id', type: TYPES::INTEGER)]
    private int $userId;

    #[ORM\Column(name: 'security_officer_email', type: TYPES::STRING, nullable: true)]
    private ?string $securityOfficerEmail = null;

    #[ORM\Column(name: 'siret_data', type: TYPES::STRING, nullable: true)]
    private ?string $siretData = null;

    #[ORM\Column(name: 'company_name', type: TYPES::STRING, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'employee_first_name', type: TYPES::STRING, nullable: true)]
    private ?string $employeeFirstName = null;

    #[ORM\Column(name: 'employee_last_name', type: TYPES::STRING, nullable: true)]
    private ?string $employeeLastName = null;

    #[ORM\Column(name: 'request_date', type: TYPES::STRING, nullable: true)]
    private ?string $requestDate = null;

    #[ORM\Column(name: "cesar_step_id")]
    private ?string $cezarStepId = null;


    public function getSecurityOfficerEmail(): ?string
    {
        return $this->securityOfficerEmail;
    }

    public function setSecurityOfficerEmail(?string $securityOfficerEmail): void
    {
        $this->securityOfficerEmail = $securityOfficerEmail;
    }

    public function getSiretData(): ?string
    {
        return $this->siretData;
    }

    public function setSiretData(?string $siretData): void
    {
        $this->siretData = $siretData;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    public function setEmployeeFirstName(?string $employeeFirstName): void
    {
        $this->employeeFirstName = $employeeFirstName;
    }

    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    public function setEmployeeLastName(?string $employeeLastName): void
    {
        $this->employeeLastName = $employeeLastName;
    }

    public function getRequestDate(): ?string
    {
        return $this->requestDate;
    }

    public function setRequestDate(?string $requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    public function getStepType(): string
    {
        return $this->stepType;
    }

    public function setStepType(string $stepType): void
    {
        $this->stepType = $stepType;
    }

    public function getStepId(): int
    {
        return $this->stepId;
    }

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function getStepNumber(): string
    {
        return $this->stepNumber;
    }

    public function setStepNumber(string $stepNumber): void
    {
        $this->stepNumber = $stepNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getCezarStepId(): ?string
    {
        return $this->cezarStepId;
    }

    public function setCezarStepId(?string $cezarStepId): void
    {
        $this->cezarStepId = $cezarStepId;
    }

    public function getStepData(): ?StepData
    {
        return $this->stepData;
    }

    public function setStepData(?StepData $stepData): void
    {
        $this->stepData = $stepData;
    }
}
