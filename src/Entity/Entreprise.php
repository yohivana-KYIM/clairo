<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[UniqueEntity(fields: ['emailReferent'], message: 'Il ne peut pas y avoir plusieurs entreprises avec le même email référent')]
#[ORM\Index(columns: ['siret'], name: 'idx_entreprise_siret')]
#[ORM\Index(columns: ['name'], name: 'idx_entreprise_name')]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeAPE = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complementNom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tvaIntraCommunautaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nature = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numTelephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomResponsable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $naf = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $emailReferent = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?AdresseEntreprise $adresse = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: DemandeTitreCirculation::class, cascade: ['persist', 'remove'])]
    private Collection $demandeTitreCirculations;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: DemandeTitreVehicule::class, cascade: ['persist', 'remove'])]
    private Collection $demandeTitreVehicules;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $suppleant1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $suppleant2 = null;

    #[ORM\OneToOne(mappedBy: 'adresseFacturationEntreprise', cascade: ['persist', 'remove'])]
    private ?AdresseFacturation $adresseFacturation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $gratuit = false;

    #[ORM\ManyToOne(targetEntity: Entreprise::class, inversedBy: 'filiales')]
    private ?Entreprise $entrepriseMere = null;

    #[ORM\OneToMany(mappedBy: 'entrepriseMere', targetEntity: Entreprise::class)]
    private Collection $filiales;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephoneReferent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephoneSuppleant1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephoneSuppleant2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonctionReferent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomSuppleant1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomSuppleant2 = null;

    public function __construct()
    {
        if (empty($this->demandeTitreCirculations)) {
            $this->demandeTitreCirculations = new ArrayCollection();
        }
        if (empty($this->demandeTitreVehicules)) {
            $this->demandeTitreVehicules = new ArrayCollection();
        }

        if (empty($this->filiales)) {
            $this->filiales = new ArrayCollection();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodeAPE(): ?string
    {
        return $this->codeAPE;
    }

    public function setCodeAPE(?string $codeAPE): static
    {
        $this->codeAPE = $codeAPE;

        return $this;
    }

    public function getSigne(): ?string
    {
        return $this->signe;
    }

    public function setSigne(?string $signe): static
    {
        $this->signe = $signe;

        return $this;
    }

    public function getComplementNom(): ?string
    {
        return $this->complementNom;
    }

    public function setComplementNom(?string $complementNom): static
    {
        $this->complementNom = $complementNom;

        return $this;
    }

    public function getTvaIntraCommunautaire(): ?string
    {
        return $this->tvaIntraCommunautaire;
    }

    public function setTvaIntraCommunautaire(?string $tvaIntraCommunautaire): static
    {
        $this->tvaIntraCommunautaire = $tvaIntraCommunautaire;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(?string $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(?string $nature): static
    {
        $this->nature = $nature;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

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

    public function getNomResponsable(): ?string
    {
        return $this->nomResponsable;
    }

    public function setNomResponsable(?string $nomResponsable): static
    {
        $this->nomResponsable = $nomResponsable;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function getNaf(): ?string
    {
        return $this->naf;
    }

    public function setNaf(?string $naf): static
    {
        $this->naf = $naf;

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

    public function getEmailReferent(): ?string
    {
        return $this->emailReferent;
    }

    public function setEmailReferent(?string $emailReferent): static
    {
        $this->emailReferent = $emailReferent;

        return $this;
    }

    public function getAdresse(): ?AdresseEntreprise
    {
        return $this->adresse;
    }

    public function setAdresse(?AdresseEntreprise $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, DemandeTitreCirculation>
     */
    public function getDemandeTitreCirculations(): Collection
    {
        return $this->demandeTitreCirculations;
    }

    public function addDemandeTitreCirculation(DemandeTitreCirculation $demandeTitreCirculation): static
    {
        if (!$this->demandeTitreCirculations->contains($demandeTitreCirculation)) {
            $this->demandeTitreCirculations->add($demandeTitreCirculation);
            $demandeTitreCirculation->setEntreprise($this);
        }

        return $this;
    }

    public function removeDemandeTitreCirculation(DemandeTitreCirculation $demandeTitreCirculation): static
    {
        if ($this->demandeTitreCirculations->removeElement($demandeTitreCirculation)) {
            // set the owning side to null (unless already changed)
            if ($demandeTitreCirculation->getEntreprise() === $this) {
                $demandeTitreCirculation->setEntreprise(null);
            }
        }

        return $this;
    }

    public function addDemandeTitreVehicule(DemandeTitreVehicule $demandeTitreVehicule): static
    {
        if (!$this->demandeTitreVehicules->contains($demandeTitreVehicule)) {
            $this->demandeTitreVehicules->add($demandeTitreVehicule);
            $demandeTitreVehicule->setEntreprise($this);
        }

        return $this;
    }

    public function removeDemandeTitreVehicule(DemandeTitreVehicule $demandeTitreVehicule): static
    {
        if ($this->demandeTitreVehicules->removeElement($demandeTitreVehicule)) {
            // set the owning side to null (unless already changed)
            if ($demandeTitreVehicule->getEntreprise() === $this) {
                $demandeTitreVehicule->setEntreprise(null);
            }
        }

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

    public function getSuppleant1(): ?string
    {
        return $this->suppleant1;
    }

    public function setSuppleant1(?string $suppleant1): static
    {
        $this->suppleant1 = $suppleant1;

        return $this;
    }

    public function getSuppleant2(): ?string
    {
        return $this->suppleant2;
    }

    public function setSuppleant2(?string $suppleant2): static
    {
        $this->suppleant2 = $suppleant2;

        return $this;
    }

    public function getAdresseFacturation(): ?AdresseFacturation
    {
        return $this->adresseFacturation;
    }

    public function setAdresseFacturation(?AdresseFacturation $adresseFacturation): static
    {
        // unset the owning side of the relation if necessary
        if ($adresseFacturation === null && $this->adresseFacturation !== null) {
            $this->adresseFacturation->setAdresseFacturationEntreprise(null);
        }

        // set the owning side of the relation if necessary
        if ($adresseFacturation !== null && $adresseFacturation->getAdresseFacturationEntreprise() !== $this) {
            $adresseFacturation->setAdresseFacturationEntreprise($this);
        }

        $this->adresseFacturation = $adresseFacturation;

        return $this;
    }

    public function isGratuit(): ?bool
    {
        return $this->gratuit;
    }

    public function setGratuit(?bool $gratuit): static
    {
        $this->gratuit = $gratuit;

        return $this;
    }

    public function getDemandeTitreVehicules(): Collection
    {
        return $this->demandeTitreVehicules;
    }

    public function setDemandeTitreVehicules(Collection $demandeTitreVehicules): void
    {
        $this->demandeTitreVehicules = $demandeTitreVehicules;
    }

    public function getFiliales(): Collection
    {
        return $this->filiales;
    }

    public function setFiliales(Collection $filiales): void
    {
        $this->filiales = $filiales;
    }

    public function getEntrepriseMere(): ?Entreprise
    {
        return $this->entrepriseMere;
    }

    public function setEntrepriseMere(?Entreprise $entrepriseMere): void
    {
        $this->entrepriseMere = $entrepriseMere;
    }

    public function addFiliale(Entreprise $filiale): static
    {
        if (!$this->filiales->contains($filiale)) {
            $this->filiales[] = $filiale;
            $filiale->setEntrepriseMere($this);
        }

        return $this;
    }

    public function removeFiliale(Entreprise $filiale): static
    {
        if ($this->filiales->removeElement($filiale)) {
            // set the owning side to null (unless already changed)
            if ($filiale->getEntrepriseMere() === $this) {
                $filiale->setEntrepriseMere(null);
            }
        }

        return $this;
    }

    public function getTelephoneReferent(): ?string
    {
        return $this->telephoneReferent;
    }

    public function setTelephoneReferent(?string $telephoneReferent): void
    {
        $this->telephoneReferent = $telephoneReferent;
    }

    public function getTelephoneSuppleant1(): ?string
    {
        return $this->telephoneSuppleant1;
    }

    public function setTelephoneSuppleant1(?string $telephoneSuppleant1): void
    {
        $this->telephoneSuppleant1 = $telephoneSuppleant1;
    }

    public function getTelephoneSuppleant2(): ?string
    {
        return $this->telephoneSuppleant2;
    }

    public function setTelephoneSuppleant2(?string $telephoneSuppleant2): void
    {
        $this->telephoneSuppleant2 = $telephoneSuppleant2;
    }

    public function getNomSuppleant2(): ?string
    {
        return $this->nomSuppleant2;
    }

    public function setNomSuppleant2(?string $nomSuppleant2): void
    {
        $this->nomSuppleant2 = $nomSuppleant2;
    }

    public function getNomSuppleant1(): ?string
    {
        return $this->nomSuppleant1;
    }

    public function setNomSuppleant1(?string $nomSuppleant1): void
    {
        $this->nomSuppleant1 = $nomSuppleant1;
    }

    public function getFonctionReferent(): ?string
    {
        return $this->fonctionReferent;
    }

    public function setFonctionReferent(?string $fonctionReferent): void
    {
        $this->fonctionReferent = $fonctionReferent;
    }

    public function getEmailEntreprise(): ?string
    {
        return $this->emailEntreprise;
    }

    public function setEmailEntreprise(?string $emailEntreprise): void
    {
        $this->emailEntreprise = $emailEntreprise;
    }
}
