<?php

namespace App\Entity;

use App\Repository\DocumentIdentiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentIdentiteRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
class DocumentIdentite extends BaseFileEntity
{
    #[ORM\OneToOne(mappedBy: 'identity', cascade: ['persist', 'remove'])]
    private ?DocumentPersonnel $identity = null;

    public function getIdentity(): ?DocumentPersonnel
    {
        return $this->identity;
    }

    public function setIdentity(?DocumentPersonnel $identity): static
    {
        // unset the owning side of the relation if necessary
        if ($identity === null && $this->identity !== null) {
            $this->identity->setIdentity(null);
        }

        // set the owning side of the relation if necessary
        if ($identity !== null && $identity->getIdentity() !== $this) {
            $identity->setIdentity($this);
        }

        $this->identity = $identity;

        return $this;
    }

}
