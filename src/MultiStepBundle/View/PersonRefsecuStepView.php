<?php

namespace App\MultiStepBundle\View;

use App\MultiStepBundle\Entity\StepData;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: "v_person_refsecu_steps")]
class PersonRefsecuStepView
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

    #[ORM\Column(name: "refsecu_id")]
    private int $refsecuId;

    #[ORM\Column(name: "refsecu_email")]
    private ?string $refsecuEmail = null;

    #[ORM\Column(name: "refsecu_siret")]
    private ?string $refsecuSiret = null;

    #[ORM\Column(name: "refsecu_email_referent")]
    private ?string $refsecuEmailReferent = null;

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

    public function getRefsecuId(): int
    {
        return $this->refsecuId;
    }

    public function getRefsecuEmail(): ?string
    {
        return $this->refsecuEmail;
    }

    public function getRefsecuSiret(): ?string
    {
        return $this->refsecuSiret;
    }

    public function getRefsecuEmailReferent(): ?string
    {
        return $this->refsecuEmailReferent;
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


    public function setStepId(int $stepId): self
    {
        $this->stepId = $stepId;
        return $this;
    }

    public function setStepNumber(string $stepNumber): self
    {
        $this->stepNumber = $stepNumber;
        return $this;
    }

    public function setStepType(string $stepType): self
    {
        $this->stepType = $stepType;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setSecurityOfficerEmail(?string $securityOfficerEmail): self
    {
        $this->securityOfficerEmail = $securityOfficerEmail;
        return $this;
    }

    public function setSiretData(?string $siretData): self
    {
        $this->siretData = $siretData;
        return $this;
    }

    public function setSiretEntreprise(?string $siretEntreprise): self
    {
        $this->siretEntreprise = $siretEntreprise;
        return $this;
    }

    public function setEmailReferentEntreprise(?string $emailReferentEntreprise): self
    {
        $this->emailReferentEntreprise = $emailReferentEntreprise;
        return $this;
    }

    public function setRefsecuId(int $refsecuId): self
    {
        $this->refsecuId = $refsecuId;
        return $this;
    }

    public function setRefsecuEmail(?string $refsecuEmail): self
    {
        $this->refsecuEmail = $refsecuEmail;
        return $this;
    }

    public function setRefsecuSiret(?string $refsecuSiret): self
    {
        $this->refsecuSiret = $refsecuSiret;
        return $this;
    }

    public function setRefsecuEmailReferent(?string $refsecuEmailReferent): self
    {
        $this->refsecuEmailReferent = $refsecuEmailReferent;
        return $this;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function setEmployeeFirstName(?string $employeeFirstName): self
    {
        $this->employeeFirstName = $employeeFirstName;
        return $this;
    }

    public function setEmployeeLastName(?string $employeeLastName): self
    {
        $this->employeeLastName = $employeeLastName;
        return $this;
    }

    public function setRequestDate(?string $requestDate): self
    {
        $this->requestDate = $requestDate;
        return $this;
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
