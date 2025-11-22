<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tourEtc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $escalierEtc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numVoie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distribution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEscalierEtc(): ?string
    {
        return $this->escalierEtc;
    }

    public function setEscalierEtc(?string $escalierEtc): static
    {
        $this->escalierEtc = $escalierEtc;

        return $this;
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

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): static
    {
        $this->cp = $cp;

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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

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
