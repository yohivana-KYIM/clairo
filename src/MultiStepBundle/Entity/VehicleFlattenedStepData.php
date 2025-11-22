<?php

namespace App\MultiStepBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_vehicle_flattened_step_data')]
class VehicleFlattenedStepData
{
    #[ORM\Id]
    #[ORM\Column(type: TYPES::INTEGER)]
	private int $step_id;

    #[ORM\Column(type: TYPES::STRING)]
	private string $step_number;

    #[ORM\Column(type: TYPES::STRING, nullable: true)]
	private ?string $cesar_step_id = null;

    #[ORM\Column(type: TYPES::STRING, nullable: true)]
	private ?string $cesar_step_line = null;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $owner_or_renter = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $company_name = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $responsible_name = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $security_officer_email = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $security_officer_phone = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $request_date = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $access_type = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $address = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $postal_code = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $city = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $country = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $siren_number = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $naf_number = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $ape_code = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $siret_number = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $vat_number = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $email = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $registration_number = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $brand = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $model = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $first_registration_date = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $vehicle_type = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $certification_type = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $gies_expiry_date = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $fos_port_access = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $lavera_port_access = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $fos_access_reason = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $lavera_access_reason = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $signature_step5 = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $card_copy = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $gies_sticker_copy = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $terms_and_conditions = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $accept_terms = null;
    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
	private ?string $signature_step6 = null;

    #[ORM\Column(type: TYPES::INTEGER)]
	private int $user_id;

    #[ORM\Column(type: TYPES::STRING, nullable: true)]
	private ?string $microcesame_id = null;

    #[ORM\Column(type: 'json', nullable: true)]
	private ?array $field_reviews = [];

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
	private ?string $status = null;

    #[ORM\Column(type: TYPES::STRING, length: 255)]
	private string $step_type;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
	private ?string $persistance_type = null;


    public function getStepId(): int
    {
        return $this->step_id;
    }

    public function getStepNumber(): string
    {
        return $this->step_number;
    }

    public function getCesarStepId(): ?string
    {
        return $this->cesar_step_id;
    }

    public function getCesarStepLine(): ?string
    {
        return $this->cesar_step_line;
    }

    public function getOwnerOrRenter(): ?string
    {
        return $this->owner_or_renter;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function getResponsibleName(): ?string
    {
        return $this->responsible_name;
    }

    public function getSecurityOfficerEmail(): ?string
    {
        return $this->security_officer_email;
    }

    public function getSecurityOfficerPhone(): ?string
    {
        return $this->security_officer_phone;
    }

    public function getRequestDate(): ?string
    {
        return $this->request_date;
    }

    public function getAccessType(): ?string
    {
        return $this->access_type;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getSirenNumber(): ?string
    {
        return $this->siren_number;
    }

    public function getNafNumber(): ?string
    {
        return $this->naf_number;
    }

    public function getApeCode(): ?string
    {
        return $this->ape_code;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siret_number;
    }

    public function getVatNumber(): ?string
    {
        return $this->vat_number;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registration_number;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getFirstRegistrationDate(): ?string
    {
        return $this->first_registration_date;
    }

    public function getVehicleType(): ?string
    {
        return $this->vehicle_type;
    }

    public function getCertificationType(): ?string
    {
        return $this->certification_type;
    }

    public function getGiesExpiryDate(): ?string
    {
        return $this->gies_expiry_date;
    }

    public function getFosPortAccess(): ?string
    {
        return $this->fos_port_access;
    }

    public function getLaveraPortAccess(): ?string
    {
        return $this->lavera_port_access;
    }

    public function getFosAccessReason(): ?string
    {
        return $this->fos_access_reason;
    }

    public function getLaveraAccessReason(): ?string
    {
        return $this->lavera_access_reason;
    }

    public function getSignatureStep5(): ?string
    {
        return $this->signature_step5;
    }

    public function getCardCopy(): ?string
    {
        return $this->card_copy;
    }

    public function getGiesStickerCopy(): ?string
    {
        return $this->gies_sticker_copy;
    }

    public function getTermsAndConditions(): ?string
    {
        return $this->terms_and_conditions;
    }

    public function getAcceptTerms(): ?string
    {
        return $this->accept_terms;
    }

    public function getSignatureStep6(): ?string
    {
        return $this->signature_step6;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getMicrocesameId(): ?string
    {
        return $this->microcesame_id;
    }

    public function getFieldReviews(): ?array
    {
        return $this->field_reviews;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStepType(): string
    {
        return $this->step_type;
    }

    public function getPersistanceType(): ?string
    {
        return $this->persistance_type;
    }

    public function setCompanyName(?string $company_name): void
    {
        $this->company_name = $company_name;
    }

    public function setResponsibleName(?string $responsible_name): void
    {
        $this->responsible_name = $responsible_name;
    }

    public function setSecurityOfficerEmail(?string $security_officer_email): void
    {
        $this->security_officer_email = $security_officer_email;
    }

    public function setSecurityOfficerPhone(?string $security_officer_phone): void
    {
        $this->security_officer_phone = $security_officer_phone;
    }

    public function setRequestDate(?string $request_date): void
    {
        $this->request_date = $request_date;
    }

    public function setAccessType(?string $access_type): void
    {
        $this->access_type = $access_type;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function setPostalCode(?string $postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function setSirenNumber(?string $siren_number): void
    {
        $this->siren_number = $siren_number;
    }

    public function setNafNumber(?string $naf_number): void
    {
        $this->naf_number = $naf_number;
    }

    public function setApeCode(?string $ape_code): void
    {
        $this->ape_code = $ape_code;
    }

    public function setSiretNumber(?string $siret_number): void
    {
        $this->siret_number = $siret_number;
    }

    public function setVatNumber(?string $vat_number): void
    {
        $this->vat_number = $vat_number;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setRegistrationNumber(?string $registration_number): void
    {
        $this->registration_number = $registration_number;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    public function setFirstRegistrationDate(?string $first_registration_date): void
    {
        $this->first_registration_date = $first_registration_date;
    }

    public function setVehicleType(?string $vehicle_type): void
    {
        $this->vehicle_type = $vehicle_type;
    }

    public function setCertificationType(?string $certification_type): void
    {
        $this->certification_type = $certification_type;
    }

    public function setGiesExpiryDate(?string $gies_expiry_date): void
    {
        $this->gies_expiry_date = $gies_expiry_date;
    }

    public function setFosPortAccess(?string $fos_port_access): void
    {
        $this->fos_port_access = $fos_port_access;
    }

    public function setLaveraPortAccess(?string $lavera_port_access): void
    {
        $this->lavera_port_access = $lavera_port_access;
    }

    public function setFosAccessReason(?string $fos_access_reason): void
    {
        $this->fos_access_reason = $fos_access_reason;
    }

    public function setLaveraAccessReason(?string $lavera_access_reason): void
    {
        $this->lavera_access_reason = $lavera_access_reason;
    }
    public function setSignatureStep5(?string $signature_step5): void
    {
        $this->signature_step5 = $signature_step5;
    }

    public function setCardCopy(?string $card_copy): void
    {
        $this->card_copy = $card_copy;
    }

    public function setGiesStickerCopy(?string $gies_sticker_copy): void
    {
        $this->gies_sticker_copy = $gies_sticker_copy;
    }

    public function setTermsAndConditions(?string $terms_and_conditions): void
    {
        $this->terms_and_conditions = $terms_and_conditions;
    }

    public function setAcceptTerms(?string $accept_terms): void
    {
        $this->accept_terms = $accept_terms;
    }

    public function setSignatureStep6(?string $signature_step6): void
    {
        $this->signature_step6 = $signature_step6;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setMicrocesameId(?string $microcesame_id): void
    {
        $this->microcesame_id = $microcesame_id;
    }

    public function setFieldReviews(?array $field_reviews): void
    {
        $this->field_reviews = $field_reviews;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function setStepType(string $step_type): void
    {
        $this->step_type = $step_type;
    }

    public function setPersistanceType(?string $persistance_type): void
    {
        $this->persistance_type = $persistance_type;
    }

    public function setStepNumber(string $step_number): void
    {
        $this->step_number = $step_number;
    }

    public function setStepId(int $step_id): void
    {
        $this->step_id = $step_id;
    }

}
