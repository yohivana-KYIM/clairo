<?php

namespace App\MultiStepBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_person_flattened_step_data')]
class PersonFlattenedStepData
{
    #[ORM\Id]
    #[ORM\Column(type: TYPES::INTEGER)]
    private int $stepId;

    #[ORM\OneToOne(targetEntity: StepData::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: "step_id", referencedColumnName: "step_id", nullable: false)]
    private ?StepData $stepData = null;

    #[ORM\Column(type: TYPES::STRING)]
    private string $stepNumber;

    #[ORM\Column(type: TYPES::STRING, nullable: true)]
    private ?string $cesarStepId = null;

    #[ORM\Column(type: TYPES::STRING, nullable: true)]
    private ?string $cesarStepLine = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $requestDate = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $naf = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $vatNumber = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $accessDurationStep1 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $accessType = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $accessPurpose = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $securityOfficerName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $securityOfficerPosition = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $securityOfficerEmail = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $securityOfficerPhone = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeFirstName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeLastName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $maidenName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $birthdate = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $birthplace = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $birthPostalCode = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $birthDistrict = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $nationality = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $socialSecurityNumber = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeEmail = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeePhone = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeAddress = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeePostalCode = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeCity = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeCountry = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $residentSituation = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $fatherName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $fatherFirstName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $motherMaidenName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $motherFirstName = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $contractType = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employeeFunction = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $employmentDate = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $contractEndDate = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $numeroCni = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $fluxelTrainingDate = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $gies_1 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $gies_2 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $atex_0 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $zar = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $health = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $idCard = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $passport = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $proofOfAddressHost = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $zarDecision = null;

    #[ORM\Column(name: 'doc_atex_0', type: TYPES::TEXT, nullable: true)]
    private ?string $doc_ates_0 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $doc_gies_1 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $doc_gies_2 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $healthAttestation = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $taxiCard = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $generalConditions = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $acceptTerms = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $cardPlace = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $accessDurationStep6 = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $signature = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $accessDecision = null;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: TYPES::STRING, length: 255)]
    private string $stepType;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
    private ?string $persistanceType = null;


    /**
     * @return int
     */
    public function getStepId(): int
    {
        return $this->stepId;
    }

    /**
     * @return string
     */
    public function getStepNumber(): string
    {
        return $this->stepNumber;
    }

    /**
     * @return string|null
     */
    public function getCesarStepId(): ?string
    {
        return $this->cesarStepId;
    }

    /**
     * @return string|null
     */
    public function getCesarStepLine(): ?string
    {
        return $this->cesarStepLine;
    }

    /**
     * @return string|null
     */
    public function getRequestDate(): ?string
    {
        return $this->requestDate;
    }

    /**
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getSiren(): ?string
    {
        return $this->siren;
    }

    /**
     * @return string|null
     */
    public function getNaf(): ?string
    {
        return $this->naf;
    }

    /**
     * @return string|null
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @return string|null
     */
    public function getAccessDurationStep1(): ?string
    {
        return $this->accessDurationStep1;
    }

    /**
     * @return string|null
     */
    public function getAccessType(): ?string
    {
        return $this->accessType;
    }

    /**
     * @return string|null
     */
    public function getAccessPurpose(): ?string
    {
        return $this->accessPurpose;
    }

    /**
     * @return string|null
     */
    public function getSecurityOfficerName(): ?string
    {
        return $this->securityOfficerName;
    }

    /**
     * @return string|null
     */
    public function getSecurityOfficerPosition(): ?string
    {
        return $this->securityOfficerPosition;
    }

    /**
     * @return string|null
     */
    public function getSecurityOfficerEmail(): ?string
    {
        return $this->securityOfficerEmail;
    }

    /**
     * @return string|null
     */
    public function getSecurityOfficerPhone(): ?string
    {
        return $this->securityOfficerPhone;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @return string|null
     */
    public function getEmployeeFirstName(): ?string
    {
        return $this->employeeFirstName;
    }

    /**
     * @return string|null
     */
    public function getEmployeeLastName(): ?string
    {
        return $this->employeeLastName;
    }

    /**
     * @return string|null
     */
    public function getMaidenName(): ?string
    {
        return $this->maidenName;
    }

    /**
     * @return string|null
     */
    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    /**
     * @return string|null
     */
    public function getBirthplace(): ?string
    {
        return $this->birthplace;
    }

    /**
     * @return string|null
     */
    public function getBirthPostalCode(): ?string
    {
        return $this->birthPostalCode;
    }

    /**
     * @return string|null
     */
    public function getBirthDistrict(): ?string
    {
        return $this->birthDistrict;
    }

    /**
     * @return string|null
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @return string|null
     */
    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }
    public function getEmployeeEmail(): ?string {
        return $this->employeeEmail;
    }
    public function getEmployeePhone(): ?string {
        return $this->employeePhone;
    }
    public function getEmployeeAddress(): ?string {
        return $this->employeeAddress;
    }
    public function getEmployeePostalCode(): ?string {
        return $this->employeePostalCode;
    }
    public function getEmployeeCity(): ?string {
        return $this->employeeCity;
    }
    public function getEmployeeCountry(): ?string {
        return $this->employeeCountry;
    }
    public function getResidentSituation(): ?string {
		return $this->residentSituation;
	}
    public function getFatherName(): ?string {
		return $this->fatherName;
	}
    public function getFatherFirstName(): ?string {
		return $this->fatherFirstName;
	}
    public function getMotherMaidenName(): ?string {
		return $this->motherMaidenName;
	}
    public function getMotherFirstName(): ?string {
		return $this->motherFirstName;
	}
    public function getContractType(): ?string {
		return $this->contractType;
	}
    public function getEmployeeFunction(): ?string {
		return $this->employeeFunction;
	}
    public function getEmploymentDate(): ?string {
		return $this->employmentDate;
	}
    public function getContractEndDate(): ?string {
		return $this->contractEndDate;
	}
    public function getNumeroCni(): ?string {
		return $this->numeroCni;
	}
    public function getFluxelTrainingDate(): ?string {
		return $this->fluxelTrainingDate;
	}
    public function getGies1(): ?string {
		return $this->gies_1;
	}
    public function getGies2(): ?string {
		return $this->gies_2;
	}
    public function getAtex0(): ?string {
		return $this->atex_0;
	}
    public function getZar(): ?string {
		return $this->zar;
	}
    public function getHealth(): ?string {
		return $this->health;
	}
    public function getIdCard(): ?string {
		return $this->idCard;
	}
    public function getPassport(): ?string {
		return $this->passport;
	}
    public function getPhoto(): ?string {
		return $this->photo;
	}
    public function getProofOfAddressHost(): ?string {
		return $this->proofOfAddressHost;
	}
    public function getZarDecision(): ?string {
		return $this->zarDecision;
	}
    public function getDocAtex0(): ?string {
		return $this->doc_ates_0;
	}
    public function getDocGies1(): ?string {
		return $this->doc_gies_1;
	}
    public function getDocGies2(): ?string {
		return $this->doc_gies_2;
	}
    public function getHealthAttestation(): ?string {
		return $this->healthAttestation;
	}
    public function getTaxiCard(): ?string {
		return $this->taxiCard;
	}
    public function getGeneralConditions(): ?string {
		return $this->generalConditions;
	}
    public function getAcceptTerms(): ?string {
		return $this->acceptTerms;
	}
    public function getCardPlace(): ?string {
		return $this->cardPlace;
	}
    public function getObservations(): ?string {
		return $this->observations;
	}
    public function getAccessDurationStep6(): ?string {
		return $this->accessDurationStep6;
	}
    public function getSignature(): ?string {
		return $this->signature;
	}
    public function getAccessDecision(): ?string {
		return $this->accessDecision;
	}
    public function getStatus(): ?string {
		return $this->status;
	}
    public function getStepType(): string {
		return $this->stepType;
	}
    public function getPersistanceType(): ?string {
		return $this->persistanceType;
	}

    public function setEmployeeEmail(?string $employeeEmail): void {
		$this->employeeEmail = $employeeEmail;
	}
    public function setEmployeePhone(?string $employeePhone): void {
		$this->employeePhone = $employeePhone;
	}
    public function setEmployeeAddress(?string $employeeAddress): void {
		$this->employeeAddress = $employeeAddress;
	}
    public function setEmployeePostalCode(?string $employeePostalCode): void {
		$this->employeePostalCode = $employeePostalCode;
	}
    public function setEmployeeCity(?string $employeeCity): void {
		$this->employeeCity = $employeeCity;
	}
    public function setEmployeeCountry(?string $employeeCountry): void {
		$this->employeeCountry = $employeeCountry;
	}
    public function setResidentSituation(?string $residentSituation): void {
		$this->residentSituation = $residentSituation;
	}
    public function setFatherName(?string $fatherName): void {
		$this->fatherName = $fatherName;
	}
    public function setFatherFirstName(?string $fatherFirstName): void {
		$this->fatherFirstName = $fatherFirstName;
	}
    public function setMotherMaidenName(?string $motherMaidenName): void {
		$this->motherMaidenName = $motherMaidenName;
	}
    public function setMotherFirstName(?string $motherFirstName): void {
		$this->motherFirstName = $motherFirstName;
	}
    public function setContractType(?string $contractType): void {
		$this->contractType = $contractType;
	}
    public function setEmployeeFunction(?string $employeeFunction): void {
		$this->employeeFunction = $employeeFunction;
	}
    public function setEmploymentDate(?string $employmentDate): void {
		$this->employmentDate = $employmentDate;
	}
    public function setContractEndDate(?string $contractEndDate): void {
		$this->contractEndDate = $contractEndDate;
	}
    public function setNumeroCni(?string $numeroCni): void {
		$this->numeroCni = $numeroCni;
	}
    public function setFluxelTrainingDate(?string $fluxelTrainingDate): void {
		$this->fluxelTrainingDate = $fluxelTrainingDate;
	}
    public function setGies1(?string $gies_1): void {
		$this->gies_1 = $gies_1;
	}
    public function setGies2(?string $gies_2): void {
		$this->gies_2 = $gies_2;
	}
    public function setAtex0(?string $atex_0): void {
		$this->atex_0 = $atex_0;
	}
    public function setZar(?string $zar): void {
		$this->zar = $zar;
	}
    public function setHealth(?string $health): void {
		$this->health = $health;
	}
    public function setIdCard(?string $idCard): void {
		$this->idCard = $idCard;
	}
    public function setPassport(?string $passport): void {
		$this->passport = $passport;
	}
    public function setPhoto(?string $photo): void {
		$this->photo = $photo;
	}
    public function setProofOfAddressHost(?string $proofOfAddressHost): void {
		$this->proofOfAddressHost = $proofOfAddressHost;
	}
    public function setZarDecision(?string $zarDecision): void {
		$this->zarDecision = $zarDecision;
	}
    public function setDocAtex0(?string $doc_ates_0): void {
		$this->doc_ates_0 = $doc_ates_0;
	}
    public function setDocGies1(?string $doc_gies_1): void {
		$this->doc_gies_1 = $doc_gies_1;
	}
    public function setDocGies2(?string $doc_gies_2): void {
		$this->doc_gies_2 = $doc_gies_2;
	}
    public function setHealthAttestation(?string $healthAttestation): void {
		$this->healthAttestation = $healthAttestation;
	}
    public function setTaxiCard(?string $taxiCard): void {
		$this->taxiCard = $taxiCard;
	}
    public function setGeneralConditions(?string $generalConditions): void {
		$this->generalConditions = $generalConditions;
	}
    public function setAcceptTerms(?string $acceptTerms): void {
		$this->acceptTerms = $acceptTerms;
	}
    public function setCardPlace(?string $cardPlace): void {
		$this->cardPlace = $cardPlace;
	}
    public function setObservations(?string $observations): void {
		$this->observations = $observations;
	}
    public function setAccessDurationStep6(?string $accessDurationStep6): void {
		$this->accessDurationStep6 = $accessDurationStep6;
	}
    public function setSignature(?string $signature): void {
		$this->signature = $signature;
	}
    public function setAccessDecision(?string $accessDecision): void {
		$this->accessDecision = $accessDecision;
	}
    public function setStatus(?string $status): void {
		$this->status = $status;
	}
    public function setStepType(string $stepType): void {
		$this->stepType = $stepType;
	}
    public function setPersistanceType(?string $persistanceType): void {
		$this->persistanceType = $persistanceType;
	}


    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setStepNumber(string $stepNumber): void
    {
        $this->stepNumber = $stepNumber;
    }

    public function setCesarStepId(?string $cesarStepId): void
    {
        $this->cesarStepId = $cesarStepId;
    }

    public function setCesarStepLine(?string $cesarStepLine): void
    {
        $this->cesarStepLine = $cesarStepLine;
    }

    public function setRequestDate(?string $requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function setSiren(?string $siren): void
    {
        $this->siren = $siren;
    }

    public function setNaf(?string $naf): void
    {
        $this->naf = $naf;
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
