<?php

namespace App\Entity;

use App\Repository\InfoComplementaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoComplementaireRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class InfoComplementaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numTelephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?bool $submited = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

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
