<?php

namespace App\Entity;

use App\Repository\FiliationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliationRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class Filiation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomPere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenomPere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomMere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenomMere = null;

    #[ORM\Column]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPere(): ?string
    {
        return $this->nomPere;
    }

    public function setNomPere(?string $nomPere): static
    {
        $this->nomPere = $nomPere;

        return $this;
    }

    public function getPrenomPere(): ?string
    {
        return $this->prenomPere;
    }

    public function setPrenomPere(?string $prenomPere): static
    {
        $this->prenomPere = $prenomPere;

        return $this;
    }

    public function getNomMere(): ?string
    {
        return $this->nomMere;
    }

    public function setNomMere(?string $nomMere): static
    {
        $this->nomMere = $nomMere;

        return $this;
    }

    public function getPrenomMere(): ?string
    {
        return $this->prenomMere;
    }

    public function setPrenomMere(?string $prenomMere): static
    {
        $this->prenomMere = $prenomMere;

        return $this;
    }

    public function isSubmited(): ?bool
    {
        return $this->submited;
    }

    public function setSubmited(bool $submited): static
    {
        $this->submited = $submited;

        return $this;
    }

}
