<?php

namespace App\Entity;

use App\Repository\DocumentPersonnelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentPersonnelRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class DocumentPersonnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $arrondissementNaissance = null;

    #[ORM\OneToOne(inversedBy: 'identity', cascade: ['persist', 'remove'])]
    private ?DocumentIdentite $identity = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?PhotoIdentite $Photo = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CasierJudiciaire $Casier = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ActeNaissance $acteNaiss = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?JustificatifDomicile $domicile = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?AttestationHebergeant $hebergement = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?IdentiteHebergeant $IdentHebergent = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?TitreSejour $sejour = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrondissementNaissance(): ?string
    {
        return $this->arrondissementNaissance;
    }

    public function setArrondissementNaissance(?string $arrondissementNaissance): static
    {
        $this->arrondissementNaissance = $arrondissementNaissance;

        return $this;
    }

    public function getIdentity(): ?DocumentIdentite
    {
        return $this->identity;
    }

    public function setIdentity(?DocumentIdentite $identity): static
    {
        $this->identity = $identity;

        return $this;
    }

    public function getPhoto(): ?PhotoIdentite
    {
        return $this->Photo;
    }

    public function setPhoto(?PhotoIdentite $Photo): static
    {
        $this->Photo = $Photo;

        return $this;
    }

    public function getCasier(): ?CasierJudiciaire
    {
        return $this->Casier;
    }

    public function setCasier(?CasierJudiciaire $Casier): static
    {
        $this->Casier = $Casier;

        return $this;
    }

    public function getActeNaiss(): ?ActeNaissance
    {
        return $this->acteNaiss;
    }

    public function setActeNaiss(?ActeNaissance $acteNaiss): static
    {
        $this->acteNaiss = $acteNaiss;

        return $this;
    }

    public function getDomicile(): ?JustificatifDomicile
    {
        return $this->domicile;
    }

    public function setDomicile(?JustificatifDomicile $domicile): static
    {
        $this->domicile = $domicile;

        return $this;
    }

    public function getHebergement(): ?AttestationHebergeant
    {
        return $this->hebergement;
    }

    public function setHebergement(?AttestationHebergeant $hebergement): static
    {
        $this->hebergement = $hebergement;

        return $this;
    }

    public function getIdentHebergent(): ?IdentiteHebergeant
    {
        return $this->IdentHebergent;
    }

    public function setIdentHebergent(?IdentiteHebergeant $IdentHebergent): static
    {
        $this->IdentHebergent = $IdentHebergent;

        return $this;
    }

    public function getSejour(): ?TitreSejour
    {
        return $this->sejour;
    }

    public function setSejour(?TitreSejour $sejour): static
    {
        $this->sejour = $sejour;

        return $this;
    }

    public function isSubmited(): ?bool
    {
        return $this->submited;
    }

    public function setSubmited(?bool $submited): static
    {
        $this->submited = $submited;

        return $this;
    }
}
