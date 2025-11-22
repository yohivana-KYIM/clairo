<?php

namespace App\AdminBundle\Domain\Model;

class Entity
{
    private ?int $id = null;
    private string $name;
    private \DateTimeInterface $createdAt;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int {
		 return $this->id;
	}
    public function getName(): string {
		 return $this->name;
	}
    public function getCreatedAt(): \DateTimeInterface {
		 return $this->createdAt;
	}

    public function rename(string $newName): void
    {
        if (empty($newName)) {
            throw new \InvalidArgumentException("Name cannot be empty.");
        }
        $this->name = $newName;
    }
}
