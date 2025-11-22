<?php

namespace App\MultiStepBundle\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'step_data')]
class StepData
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $stepId = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: TYPES::STRING)]
    private string $stepNumber;

    #[ORM\Column(type: TYPES::STRING)]
    private string $stepType;

    #[ORM\Column(type: TYPES::STRING)]
    private string $persistanceType;

    #[ORM\Column(type: 'json')]
    private array $data = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $fieldReviews = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microcesameId = null;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
    private ?string $cesarStepId = null;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true)]
    private ?string $cesarStepLine = null;

    public function getCesarStepId(): ?string
    {
        return $this->cesarStepId;
    }

    public function setCesarStepId(?string $cesarStepId): void
    {
        $this->cesarStepId = $cesarStepId;
    }

    public function getCesarStepLine(): ?string
    {
        return $this->cesarStepLine;
    }

    public function setCesarStepLine(?string $cesarStepLine): void
    {
        $this->cesarStepLine = $cesarStepLine;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getStepId(): string
    {
        return $this->stepId ?? '';
    }

    public function getMicrocesameId(): ?string
    {
        return $this->microcesameId;
    }

    public function setMicrocesameId(?string $microcesameId): void
    {
        $this->microcesameId = $microcesameId;
    }

    public function setStepId(string $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getInternalData($section, $hash): mixed {
        $return = $this->data[$section];
        foreach (explode('.', $hash) as $key) {
            if (!isset($return[$key])) {
                return '';
            }
            $return = $return[$key];
        }
        return $return ?? '';
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getStepType(): string
    {
        return $this->stepType;
    }

    public function setStepType(string $stepType): void
    {
        $this->stepType = $stepType;
    }

    public function getStepNumber(): string
    {
        return $this->stepNumber ?? '';
    }

    public function setStepNumber(string $stepNumber): void
    {
        $this->stepNumber = $stepNumber;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPersistanceType(): string
    {
        return $this->persistanceType;
    }

    public function setPersistanceType(string $persistanceType): void
    {
        $this->persistanceType = $persistanceType;
    }

    public function getFieldReviews(): ?array
    {
        return $this->fieldReviews;
    }

    public function setFieldReviews(?array $fieldReviews): void
    {
        $this->fieldReviews = $fieldReviews;
    }

    public function generateCards(): array
    {
        $lastname   = strtoupper($this->getInternalData('person_step_two', 'employee_last_name'));
        $firstname  = strtoupper($this->getInternalData('person_step_two', 'employee_first_name'));
        $company    = strtoupper($this->getInternalData('person_step_one', 'company_name'));
        $duration   = $this->getInternalData('person_step_one', 'access_duration');
        $type       = $this->getInternalData('person_step_one', 'access_type');
        $serial     = $this->getInternalData('person_step_two', 'numero_cni');
        $photoUrl   = $this->normalizePublicPath($this->getInternalData('person_step_five', 'photo') ?: '/img/default-photo.png');
        $logoUrl    = '/img/logo2.png';

        // Construction du libellé de carte
        $cardType = match ($duration) {
            'permanent'  => 'Carte Permanente',
            'temporaire' => 'Carte Temporaire',
            default      => 'Carte',
        };

        if ($type === 'renewal') {
            $cardType .= ' (Renouvellement)';
        } elseif ($type === 'duplicate') {
            $reason = $this->getInternalData('person_step_one', 'duplicate_reason');
            $reasonLabel = match ($reason) {
                'loss'   => ' - Duplicata (Perte)',
                'breaks' => ' - Duplicata (Casse)',
                'theft'  => ' - Duplicata (Vol)',
                default  => ' - Duplicata',
            };
            $cardType .= $reasonLabel;
        }

        // Gestion de la date d’expiration (si permanent → fixe, sinon calculable)
        $expiresAt = $duration === 'permanent'
            ? '17/01/30'
            : $this->getInternalData('person_step_two', 'contract_end_date');

        $cards = [];
        $locations = $this->getInternalData('person_step_one', 'access_locations');
        if (!is_array($locations)) {
            $locations = [];
        }

        foreach ($locations as $location) {
            if ($location === 'fos') {
                $cards[] = [
                    'layout'     => 'recto-right',
                    'site_title' => 'Port Pétrolier de Fos',
                    'site_code'  => '0605',
                    'omi'        => 'FRMRS 0012',
                    'lastname'   => $lastname,
                    'firstname'  => $firstname,
                    'company'    => $company,
                    'card_type'  => $cardType,
                    'expires_at' => $expiresAt,
                    'serial'     => $serial,
                    'photo_url'  => $photoUrl,
                    'logo_url'   => $logoUrl,
                    'bg_url'     => '/img/backgroundfos.jpg',
                ];
            }

            if ($location === 'lavera') {
                $cards[] = [
                    'layout'     => 'recto-left',
                    'site_title' => 'Port Pétrolier de Lavéra',
                    'site_code'  => '0603',
                    'omi'        => 'FRMRS 0010',
                    'lastname'   => $lastname,
                    'firstname'  => $firstname,
                    'company'    => $company,
                    'card_type'  => $cardType,
                    'expires_at' => $expiresAt,
                    'serial'     => $serial,
                    'photo_url'  => $photoUrl,
                    'logo_url'   => $logoUrl,
                    'bg_url'     => '/img/backgroundlavera.jpg',
                ];
            }
        }

        return $cards;
    }

    private function normalizePublicPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $base = $_ENV['APP_PUBLIC_PATH'] ?? '/srv/app/public';
        return str_starts_with($path, $base)
            ? substr($path, strlen($base))
            : $path;
    }

    public function toFluxelTemplateArray($refsecEmail, $teamEmails, $ccEmails): array
    {
        $stepOne  = $this->data['person_step_one']  ?? [];
        $stepTwo  = $this->data['person_step_two']  ?? [];
        $stepThree = $this->data['person_step_three'] ?? [];
        $stepFive = $this->data['person_step_five'] ?? [];
        $stepSix  = $this->data['person_step_six']  ?? [];

        $sdriToEmails = $sdriCcEmails = null;
        if ($refsecEmail) {
            $sdriToEmails = explode(',', $teamEmails);
            $sdriCcEmails = explode(',', $ccEmails);
            $sdriToEmails = reset($sdriToEmails);
            $sdriCcEmails = reset($sdriCcEmails);
        }

        // ----------------------
        // 1. Infos Employeur
        // ----------------------
        $employer = [
            'company_name'            => $stepOne['company_name'] ?? '',
            'siren'                   => $stepOne['siren'] ?? '',
            'siret'                   => $stepOne['siret'] ?? '',
            'naf'                     => $stepOne['naf'] ?? '',
            'vat_number'              => $stepOne['vat_number'] ?? '',
            'address'                 => $stepOne['address'] ?? '',
            'postal_code'             => $stepOne['postal_code'] ?? '',
            'city'                    => $stepOne['city'] ?? '',
            'country'                 => $stepOne['country'] ?? '',
            'security_officer_name'   => $stepOne['security_officer_name'] ?? '',
            'security_officer_position'=> $stepOne['security_officer_position'] ?? '',
            'security_officer_email'  => $sdriToEmails ?? $stepOne['security_officer_email'] ?? '',
            'security_officer_phone'  => $stepOne['security_officer_phone'] ?? '',
            'alternate_referent_name' => $stepOne['alternate_referent_name'] ?? '',
            'alternate_referent_position' => $stepOne['alternate_referent_position'] ?? '',
            'alternate_referent_email'=> $sdriCcEmails ?? $stepOne['alternate_referent_email'] ?? '',
            'alternate_referent_phone'=> $stepOne['alternate_referent_phone'] ?? '',
        ];

        // ----------------------
        // 2. Infos Salarié
        // ----------------------
        $employee = [
            'gender'         => $stepTwo['gender'] ?? '',
            'first_name'     => $stepTwo['employee_first_name'] ?? '',
            'last_name'      => $stepTwo['employee_last_name'] ?? '',
            'maiden_name'    => $stepTwo['maiden_name'] ?? '',
            'birthdate'      => $stepTwo['employee_birthdate'] ?? '',
            'birthplace'     => $stepTwo['employee_birthplace'] ?? '',
            'birth_postal'   => $stepTwo['employee_birth_postale_code'] ?? '',
            'birth_district' => $stepTwo['employee_birth_district'] ?? '',
            'nationality'    => $stepTwo['nationality'] ?? '',
            'email'          => $stepTwo['employee_email'] ?? '',
            'phone'          => $stepTwo['employee_phone'] ?? '',
            'address'        => $stepTwo['section_employee_address'] ?? '',
            'postal_code'    => $stepTwo['postal_code'] ?? '',
            'city'           => $stepTwo['city'] ?? '',
            'country'        => $stepTwo['country'] ?? '',
            'father_name'    => $stepTwo['father_name'] ?? '',
            'father_first'   => $stepTwo['father_first_name'] ?? '',
            'mother_name'    => $stepTwo['mother_maiden_name'] ?? '',
            'mother_first'   => $stepTwo['mother_first_name'] ?? '',
            'id_number'      => $stepTwo['numero_cni'] ?? '',
            'function'       => $stepTwo['employee_function'] ?? '',
            'contract_type'  => $stepTwo['contract_type'] ?? '',
            'employment_date'=> $stepTwo['employment_date'] ?? '',
            'contract_end'   => $stepTwo['contract_end_date'] ?? '',
        ];

        // ----------------------
        // 3. Accès
        // ----------------------
        $access = [
            'duration'        => $stepOne['access_duration'] ?? '',
            'type'            => $stepOne['access_type'] ?? '',
            'duplicate_reason'=> $stepOne['duplicate_reason'] ?? '',
            'locations'       => $stepOne['access_locations'] ?? [],
            'purpose'         => $this->decodeEncodedContent($stepOne['access_purpose'] ?? ''),
        ];

        // ----------------------
        // 4. Formations & Certifs
        // ----------------------
        $training = [
            'fluxel' => $stepThree['fluxel_training'] ?? '',
            'gies'   => $stepThree['gies'] ?? '',
            'atex'   => $stepThree['atex'] ?? '',
            'zar'    => $stepThree['zar'] ?? '',
            'health' => $stepThree['health'] ?? '',
        ];

        // ----------------------
        // 5. Pièces justificatives
        // ----------------------
        $docs = [
            'id_card'          => $stepFive['id_card'] ?? null,
            'passport'         => $stepFive['passport'] ?? null,
            'residence_permit' => $stepFive['residence_permit'] ?? null,
            'photo'            => $stepFive['photo'] ?? null,
            'bank_receipt'     => $stepFive['bank_receipt'] ?? null,
            'proof_of_address_host'  => $stepFive['proof_of_address_host'] ?? null,
            'resident_id_card' => $stepFive['resident_id_card'] ?? null,
            'resident_letter'  => $stepFive['resident_letter'] ?? null,
            'proof_of_address_hosted'=> $stepFive['proof_of_address_hosted'] ?? null,
            'zar_decision'     => $stepFive['zar_decision'] ?? null,
            'doc_atex_0'       => $stepFive['doc_atex_0'] ?? null,
            'doc_gies_1'       => $stepFive['doc_gies_1'] ?? null,
            'previous_card'    => $stepFive['previous_card'] ?? null,
            'loss_declaration' => $stepFive['loss_declaration'] ?? null,
            'health_attestation'=> $stepFive['health_attestation'] ?? null,
            'taxi_card'        => $stepFive['taxi_card'] ?? null,
            'birth_certificate'=> $stepFive['birth_certificate'] ?? null,
            'criminal_record_origin'    => $stepFive['criminal_record_origin'] ?? null,
            'criminal_record_nationality'=> $stepFive['criminal_record_nationality'] ?? null,
            'criminal_record_resident_country'=> $stepFive['criminal_record_resident_country'] ?? null,
            'refugee_attestation' => $stepFive['refugee_attestation'] ?? null,
            'refugee_criminal_record' => $stepFive['refugee_criminal_record'] ?? null,
        ];

        // ----------------------
        // 6. Validation / Admin
        // ----------------------
        $admin = [
            'observations'    => $stepSix['observations'] ?? '',
            'access_decision' => $stepSix['access_decision'] ?? '',
            'access_expiration_date' => $stepSix['access_expiration_date'] ?? '',
            'card_place'      => $stepSix['card_place'] ?? '',
            'signature_admin' => $stepSix['signature_admin'] ?? '',
        ];

        return [
            'step_id'   => $this->getStepId(),
            'step_number' => $this->getStepNumber(),
            'status'    => $this->getStatus(),
            'employer'  => $employer,
            'employee'  => $employee,
            'access'    => $access,
            'training'  => $training,
            'docs'      => $docs,
            'admin'     => $admin,
        ];
    }

    /**
     * Décode et nettoie un contenu encodé en Base64 encapsulé dans <span>.
     * - Supprime les balises <span>
     * - Décode le contenu si valide
     * - Retourne le texte brut sinon
     */
    function decodeEncodedContent(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        // Supprimer les balises HTML (comme <span>)
        $stripped = strip_tags($value);

        // Essayer de décoder en Base64
        $decoded = base64_decode($stripped, true);

        return $decoded !== false ? $decoded : $stripped;
    }

    public function setInternalData(string $section, string $path, mixed $value): void
    {
        $data = $this->data;
        if (!isset($data[$section]) || !is_array($data[$section])) {
            $data[$section] = [];
        }

        $ref =& $data[$section];
        $keys = explode('.', $path);
        foreach ($keys as $k) {
            if (!isset($ref[$k]) || !is_array($ref[$k])) {
                $ref[$k] = [];
            }
            $ref =& $ref[$k];
        }

        $ref = $value;
        $this->data = $data;
    }
}
