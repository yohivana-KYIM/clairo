<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(targetEntity: EntrepriseUnifiee::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
    private ?EntrepriseUnifiee $entreprise = null;

    #[ORM\Column]
    private int $stepId;

    #[ORM\Column(length: 20)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
    private string $stepType;

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column(nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?string $prenom = null;

    public function getId(): ?int {
		 return $this->id;
	}

    public function getProduit(): ?Produit {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self {
        $this->produit = $produit; return $this;
    }

    public function getStepId(): int {
        return $this->stepId;
    }

    public function setStepId(int $stepId): self {
        $this->stepId = $stepId; return $this;
    }

    public function getStepType(): string {
        return $this->stepType;
    }

    public function setStepType(string $stepType): self {
        $this->stepType = $stepType;
        return $this;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $q): self {
        $this->quantity = $q; return $this;
    }

    public function increment(): self {
        $this->quantity++;
        return $this;
    }

    public function decrement(): self {
        if ($this->quantity > 0) $this->quantity--;
        return $this;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): self {
        $this->nom = $nom; return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEntreprise(): ?EntrepriseUnifiee {
        return $this->entreprise;
    }

    public function getEntrepriseId(): ?int {
        return $this->entreprise->getId();
    }

    public function setEntreprise(?EntrepriseUnifiee $entreprise): self {
        $this->entreprise = $entreprise;
        return $this;
    }
}
