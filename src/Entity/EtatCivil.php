<?php

namespace App\Entity;

use App\Repository\EtatCivilRepository;
use DateTimeInterface as DateTimeInterfaceAlias;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatCivilRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class EtatCivil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom4 = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterfaceAlias $dateNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paysNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cpNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $arrondissementNaissance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomMarital = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(?string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPrenom2(): ?string
    {
        return $this->prenom2;
    }

    public function setPrenom2(?string $prenom2): static
    {
        $this->prenom2 = $prenom2;

        return $this;
    }

    public function getPrenom3(): ?string
    {
        return $this->prenom3;
    }

    public function setPrenom3(?string $prenom3): static
    {
        $this->prenom3 = $prenom3;

        return $this;
    }

    public function getPrenom4(): ?string
    {
        return $this->prenom4;
    }

    public function setPrenom4(?string $prenom4): static
    {
        $this->prenom4 = $prenom4;

        return $this;
    }

    public function getDateNaissance(): ?DateTimeInterfaceAlias
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?DateTimeInterfaceAlias $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPaysNaissance(): ?string
    {
        return $this->paysNaissance;
    }

    public function setPaysNaissance(?string $paysNaissance): static
    {
        $this->paysNaissance = $paysNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(?string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getCpNaissance(): ?string
    {
        return $this->cpNaissance;
    }

    public function setCpNaissance(?string $cpNaissance): static
    {
        $this->cpNaissance = $cpNaissance;

        return $this;
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

    public function getNomMarital(): ?string
    {
        return $this->nomMarital;
    }

    public function setNomMarital(?string $nomMarital): static
    {
        $this->nomMarital = $nomMarital;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): static
    {
        $this->nationalite = $nationalite;

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
