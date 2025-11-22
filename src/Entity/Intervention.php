<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\InterventionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $batAdministration = true;

    #[ORM\Column(nullable: true)]
    private ?bool $exploitationFos = true;

    #[ORM\Column(nullable: true)]
    private ?bool $exploitationLavera = true;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateIntervention = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isBatAdministration(): ?bool
    {
        return $this->batAdministration;
    }

    public function setBatAdministration(?bool $batAdministration): static
    {
        $this->batAdministration = $batAdministration;

        return $this;
    }

    public function isExploitationFos(): ?bool
    {
        return $this->exploitationFos;
    }

    public function setExploitationFos(?bool $exploitationFos): static
    {
        $this->exploitationFos = $exploitationFos;

        return $this;
    }

    public function isExploitationLavera(): ?bool
    {
        return $this->exploitationLavera;
    }

    public function setExploitationLavera(?bool $exploitationLavera): static
    {
        $this->exploitationLavera = $exploitationLavera;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getAutre(): ?string
    {
        return $this->autre;
    }

    public function setAutre(?string $autre): static
    {
        $this->autre = $autre;

        return $this;
    }

    public function getDateIntervention(): ?DateTimeInterface
    {
        return $this->dateIntervention;
    }

    public function setDateIntervention(?DateTimeInterface $dateIntervention): static
    {
        $this->dateIntervention = $dateIntervention;

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
