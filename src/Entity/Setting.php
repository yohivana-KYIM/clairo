<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_settings')]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'reference_data')]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: TYPES::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: TYPES::STRING, length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: TYPES::TEXT, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: TYPES::STRING, length: 50)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
    private string $type;

    #[ORM\Column(type: TYPES::STRING, length: 50)]
    private string $groupName;

    #[ORM\Column(type: TYPES::STRING, length: 150)]
    private string $label;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    public function getId(): ?int {
		 return $this->id;
	}
    public function getName(): string {
		 return $this->name;
	}
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getGroupName(): string {
		 return $this->groupName;
	}
    public function setGroupName(string $groupName): self { $this->groupName = $groupName; return $this; }
    public function getType(): string {
		 return $this->type;
	}
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getLabel(): string {
		 return $this->label;
	}
    public function setLabel(string $label): self { $this->label = $label; return $this; }

    public function getValue(): mixed
    {
        return match ($this->type) {
            'int' => (int) $this->value,
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public function setValue(mixed $value): self
    {
        $this->value = match ($this->type) {
            'json' => json_encode($value),
            default => (string) $value,
        };
        return $this;
    }

    public function getRawValue(): ?string {
		 return $this->value;
	}
    public function setRawValue(?string $value): self { $this->value = $value; return $this; }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}
