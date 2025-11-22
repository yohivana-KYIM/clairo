<?php

namespace App\MultiStepBundle\View;

use App\MultiStepBundle\Entity\StepData;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: "v_person_user_steps")]
class PersonUserStepView
{
    #[ORM\Id]
    #[ORM\Column(name: "step_id")]
    private int $stepId;

    #[ORM\OneToOne(targetEntity: StepData::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: "step_id", referencedColumnName: "step_id", nullable: false)]
    private ?StepData $stepData = null;

    #[ORM\Column(name: "step_number")]
    private string $stepNumber;

    #[ORM\Column(name: "step_type")]
    private string $stepType;

    #[ORM\Column(name: "status")]
    private string $status;

    #[ORM\Column(name: "user_id")]
    private int $userId;

    #[ORM\Column(name: "security_officer_email")]
    private ?string $securityOfficerEmail = null;

    #[ORM\Column(name: "siret_data")]
    private ?string $siretData = null;

    #[ORM\Column(name: "siret_entreprise")]
    private ?string $siretEntreprise = null;

    #[ORM\Column(name: "email_referent_entreprise")]
    private ?string $emailReferentEntreprise = null;

    #[ORM\Column(name: "company_name")]
    private ?string $companyName = null;

    #[ORM\Column(name: "employee_first_name")]
    private ?string $employeeFirstName = null;

    #[ORM\Column(name: "employee_last_name")]
    private ?string $employeeLastName = null;

    #[ORM\Column(name: "request_date")]
    private ?string $requestDate = null;

    #[ORM\Column(name: "cesar_step_id")]
    private ?string $cezarStepId = null;

    // ➜ Getters

    public function getStepId(): int
    {
        return $this->stepId;
    }

    public function getStepNumber(): string
    {
        return $this->stepNumber;
    }

    public function getStepType(): string
    {
        return $this->stepType;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSecurityOfficerEmail(): ?string
    {
        return $this->securityOfficerEmail;
    }

    public function getSiretData(): ?string
    {
        return $this->siretData;
    }

    public function getSiretEntreprise(): ?string
    {
        return $this->siretEntreprise;
    }

    public function getEmailReferentEntreprise(): ?string
    {
        return $this->emailReferentEntreprise;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    public function getRequestDate(): ?string
    {
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

    public function setSecurityOfficerEmail(?string $securityOfficerEmail): void
    {
        $this->securityOfficerEmail = $securityOfficerEmail;
    }

    public function setSiretData(?string $siretData): void
    {
        $this->siretData = $siretData;
    }

    public function setSiretEntreprise(?string $siretEntreprise): void
    {
        $this->siretEntreprise = $siretEntreprise;
    }

    public function setEmailReferentEntreprise(?string $emailReferentEntreprise): void
    {
        $this->emailReferentEntreprise = $emailReferentEntreprise;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setEmployeeFirstName(?string $employeeFirstName): void
    {
        $this->employeeFirstName = $employeeFirstName;
    }

    public function setEmployeeLastName(?string $employeeLastName): void
    {
        $this->employeeLastName = $employeeLastName;
    }

    public function setRequestDate(?string $requestDate): void
    {
        $this->requestDate = $requestDate;
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
