<?php

namespace App\Entity;

use App\Repository\EntrepriseUnifieeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseUnifieeRepository::class, readOnly: true)]
#[ORM\Table(name: "v_entreprise")]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class EntrepriseUnifiee
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name: "hash_key", type: "string", nullable: true)]
    private ?string $cleHash = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: "code_ape", type: "string", nullable: true)]
    private ?string $codeApe = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $naf = null;

    #[ORM\Column(name: "tva_intra_communautaire", type: "string", nullable: true)]
    private ?string $tvaIntra = null;

    #[ORM\Column(name: "nom_responsable", type: "string", nullable: true)]
    private ?string $nomResponsable = null;

    #[ORM\Column(name: "email_referent", type: "string", nullable: true)]
    private ?string $emailReferent = null;

    #[ORM\Column(name: "suppleant1", type: "string", nullable: true)]
    private ?string $suppleant1 = null;

    #[ORM\Column(name: "telephone_referent", type: "string", nullable: true)]
    private ?string $telephoneReferent = null;

    #[ORM\Column(name: "telephone_suppleant1", type: "string", nullable: true)]
    private ?string $telephoneSuppleant1 = null;

    #[ORM\Column(name: "address", type: "string", nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: "postal_code", type: "string", nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(name: "city", type: "string", nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "country", type: "string", nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(name: "source_table", type: "string", nullable: true)]
    private ?string $tableSource = null;
    // --- Getters uniquement (entité en lecture seule) ---
    public function getId(): int {
		 return $this->id;
	}
    public function getCleHash(): ?string {
		 return $this->cleHash;
	}
    public function getNom(): ?string {
		 return $this->nom;
	}
    public function getCodeApe(): ?string {
		 return $this->codeApe;
	}
    public function getSiret(): ?string {
		 return $this->siret;
	}
    public function getSiren(): ?string {
		 return $this->siren;
	}
    public function getNaf(): ?string {
		 return $this->naf;
	}
    public function getTvaIntraCommunautaire(): ?string {
		 return $this->tvaIntra;
	}
    public function getTvaIntra(): ?string {
		 return $this->tvaIntra;
	}
    public function getNomResponsable(): ?string {
		 return $this->nomResponsable;
	}
    public function getEmailReferent(): ?string {
		 return $this->emailReferent;
	}
    public function getSuppleant1(): ?string {
		 return $this->suppleant1;
	}
    public function getTelephoneReferent(): ?string {
		 return $this->telephoneReferent;
	}
    public function getTelephoneSuppleant1(): ?string {
		 return $this->telephoneSuppleant1;
	}
    public function getAdresse(): ?string {
		 return $this->adresse;
	}
    public function getCodePostal(): ?string {
		 return $this->codePostal;
	}
    public function getVille(): ?string {
		 return $this->ville;
	}
    public function getPays(): ?string {
		 return $this->pays;
	}
    public function getNationalite(): ?string {
		 return $this->pays;
	}
    public function getTableSource(): ?string {
		 return $this->tableSource;
	}

    public function setTelephoneSuppleant1(?string $telephoneSuppleant1): void
    {
        $this->telephoneSuppleant1 = $telephoneSuppleant1;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCleHash(?string $cleHash): void
    {
        $this->cleHash = $cleHash;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function setCodeApe(?string $codeApe): void
    {
        $this->codeApe = $codeApe;
    }

    public function setSiret(?string $siret): void
    {
        $this->siret = $siret;
    }

    public function setSiren(?string $siren): void
    {
        $this->siren = $siren;
    }

    public function setNaf(?string $naf): void
    {
        $this->naf = $naf;
    }

    public function setTvaIntra(?string $tvaIntra): void
    {
        $this->tvaIntra = $tvaIntra;
    }

    public function setNomResponsable(?string $nomResponsable): void
    {
        $this->nomResponsable = $nomResponsable;
    }

    public function setEmailReferent(?string $emailReferent): void
    {
        $this->emailReferent = $emailReferent;
    }

    public function setSuppleant1(?string $suppleant1): void
    {
        $this->suppleant1 = $suppleant1;
    }

    public function setTelephoneReferent(?string $telephoneReferent): void
    {
        $this->telephoneReferent = $telephoneReferent;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function setCodePostal(?string $codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }

    public function setPays(?string $pays): void
    {
        $this->pays = $pays;
    }

    public function setTableSource(?string $tableSource): void
    {
        $this->tableSource = $tableSource;
    }

    public function getAdresseObject(): Adresse
    {
        $adresse = new Adresse();

        $adresse->setNumVoie($this->adresse);
        $adresse->setCp($this->codePostal);
        $adresse->setVille($this->ville);
        $adresse->setPays($this->pays);
        $adresse->setDistribution(null); // pas dispo dans la vue
        $adresse->setTourEtc(null);      // idem
        $adresse->setEscalierEtc(null);  // idem
        $adresse->setSubmited(null);

        return $adresse;
    }

}
