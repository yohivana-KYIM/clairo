<?php

namespace App\Entity;

use App\Repository\AdresseEntrepriseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseEntrepriseRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class AdresseEntreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numVoie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distribution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tourEtc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numTelephone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumVoie(): ?string
    {
        return $this->numVoie;
    }

    public function setNumVoie(?string $numVoie): static
    {
        $this->numVoie = $numVoie;

        return $this;
    }

    public function getDistribution(): ?string
    {
        return $this->distribution;
    }

    public function setDistribution(?string $distribution): static
    {
        $this->distribution = $distribution;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTourEtc(): ?string
    {
        return $this->tourEtc;
    }

    public function setTourEtc(?string $tourEtc): static
    {
        $this->tourEtc = $tourEtc;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public  function getPays(): string
    {
        return 'France';
    }

    public function setCp(?string $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    public function getNumTelephone(): ?string
    {
        return $this->numTelephone;
    }

    public function setNumTelephone(?string $numTelephone): static
    {
        $this->numTelephone = $numTelephone;

        return $this;
    }

    public function getAdresseComplete(): string
    {
        $parts = [];

        if ($this->numVoie) {
            $parts[] = $this->numVoie;
        }

        if ($this->tourEtc) {
            $parts[] = $this->tourEtc;
        }

        if ($this->distribution) {
            $parts[] = $this->distribution;
        }

        if ($this->cp || $this->ville) {
            $codeVille = trim(($this->cp ?? '') . ' ' . ($this->ville ?? ''));
            $parts[] = $codeVille;
        }

        return implode(', ', array_filter($parts));
    }
}
