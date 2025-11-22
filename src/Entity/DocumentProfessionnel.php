<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\DocumentProfessionnelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentProfessionnelRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class DocumentProfessionnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateGies0Debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateGies0Fin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateAtex0Debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dateAtex0Fin = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Gies0 $gies0 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Gies1 $gies1 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Gies2 $gies2 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Atex0 $atex0 = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?AutreDocument $autre = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_gies1_fin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_gies2_fin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateGies0Debut(): ?DateTimeInterface
    {
        return $this->dateGies0Debut;
    }

    public function setDateGies0Debut(?DateTimeInterface $dateGies0Debut): static
    {
        $this->dateGies0Debut = $dateGies0Debut;

        return $this;
    }

    public function getDateGies0Fin(): ?DateTimeInterface
    {
        return $this->dateGies0Fin;
    }

    public function setDateGies0Fin(?DateTimeInterface $dateGies0Fin): static
    {
        $this->dateGies0Fin = $dateGies0Fin;

        return $this;
    }

    public function getDateAtex0Debut(): ?DateTimeInterface
    {
        return $this->dateAtex0Debut;
    }

    public function setDateAtex0Debut(?DateTimeInterface $dateAtex0Debut): static
    {
        $this->dateAtex0Debut = $dateAtex0Debut;

        return $this;
    }

    public function getDateAtex0Fin(): ?DateTimeInterface
    {
        return $this->dateAtex0Fin;
    }

    public function setDateAtex0Fin(?DateTimeInterface $dateAtex0Fin): static
    {
        $this->dateAtex0Fin = $dateAtex0Fin;

        return $this;
    }

    public function getGies0(): ?Gies0
    {
        return $this->gies0;
    }

    public function setGies0(?Gies0 $gies0): static
    {
        $this->gies0 = $gies0;

        return $this;
    }

    public function getGies1(): ?Gies1
    {
        return $this->gies1;
    }

    public function setGies1(?Gies1 $gies1): static
    {
        $this->gies1 = $gies1;

        return $this;
    }

    public function getGies2(): ?Gies2
    {
        return $this->gies2;
    }

    public function setGies2(?Gies2 $gies2): static
    {
        $this->gies2 = $gies2;

        return $this;
    }

    public function getAtex0(): ?Atex0
    {
        return $this->atex0;
    }

    public function setAtex0(?Atex0 $atex0): static
    {
        $this->atex0 = $atex0;

        return $this;
    }

    public function getAutre(): ?AutreDocument
    {
        return $this->autre;
    }

    public function setAutre(?AutreDocument $autre): static
    {
        $this->autre = $autre;

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

    public function getDateGies1Fin(): ?DateTimeInterface
    {
        return $this->date_gies1_fin;
    }

    public function setDateGies1Fin(?DateTimeInterface $date_gies1_fin): static
    {
        $this->date_gies1_fin = $date_gies1_fin;

        return $this;
    }

    public function getDateGies2Fin(): ?DateTimeInterface
    {
        return $this->date_gies2_fin;
    }

    public function setDateGies2Fin(?DateTimeInterface $date_gies2_fin): static
    {
        $this->date_gies2_fin = $date_gies2_fin;

        return $this;
    }

}
