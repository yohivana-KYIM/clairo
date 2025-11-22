<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\DemandeTitreVehiculeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeTitreVehiculeRepository::class)]
class DemandeTitreVehicule
{
    const STATUS_CARD_EDITED = 'Edité';
    const STATUS_CARD_DELIVERED = 'Carte délivrée';
    const STATUS_DEPOSIT = 'Dépôt en attente de traitement';
    const STATUS_PENDING = 'En cours d’instruction';
    const STATUS_AWAITING = 'Attente information complémentaire';
    const STATUS_PROVISIONED = 'Accord accès temporaire';
    const STATUS_GRANTED = 'accord';
    const STATUS_DENIED = 'refuse';
    const STATUS_EMPLOYER_REFERENCE = 'Attente référent de l\'entreprise';
    const STATUS_AWAITING_PAYMENT = 'En attente de paiement';
    const STATUS_PAID = 'Payé';
    const STATUS_BAD_FIRM = 'Mauvaise entreprise';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Intervention $intervention = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?EtatCivil $etatcivil = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Filiation $filiation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Adresse $adresse = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?InfoComplementaireVehicule $infocomplementaire = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?DocumentPersonnel $docpersonnel = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?DocumentProfessionnel $documentprofessionnel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $validated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\ManyToOne(inversedBy: 'demandeVehicules')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
    #[ORM\ManyToOne(inversedBy: 'demandeTitreVehicules')]
    private ?Entreprise $entreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numExport = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): static
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getEtatCivil(): ?EtatCivil
    {
        return $this->etatcivil;
    }

    public function setEtatCivil(?EtatCivil $etatcivil): static
    {
        $this->etatcivil = $etatcivil;

        return $this;
    }

    public function getFiliation(): ?Filiation
    {
        return $this->filiation;
    }

    public function setFiliation(?Filiation $filiation): static
    {
        $this->filiation = $filiation;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getInfocomplementaire(): ?InfoComplementaireVehicule
    {
        return $this->infocomplementaire;
    }

    public function setInfocomplementaire(?InfoComplementaireVehicule $infocomplementaire): static
    {
        $this->infocomplementaire = $infocomplementaire;

        return $this;
    }

    public function getDocpersonnel(): ?DocumentPersonnel
    {
        return $this->docpersonnel;
    }

    public function setDocpersonnel(?DocumentPersonnel $docpersonnel): static
    {
        $this->docpersonnel = $docpersonnel;

        return $this;
    }

    public function getDocumentprofessionnel(): ?DocumentProfessionnel
    {
        return $this->documentprofessionnel;
    }

    public function setDocumentprofessionnel(?DocumentProfessionnel $documentprofessionnel): static
    {
        $this->documentprofessionnel = $documentprofessionnel;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getValidatedAt(): ?DateTimeInterface
    {
        return $this->validated_at;
    }

    public function setValidatedAt(?DateTimeInterface $validated_at): static
    {
        $this->validated_at = $validated_at;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getNumExport(): ?string
    {
        return $this->numExport;
    }

    public function setNumExport(?string $numExport): static
    {
        $this->numExport = $numExport;

        return $this;
    }
}
