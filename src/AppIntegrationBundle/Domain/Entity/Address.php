<?php

namespace App\AppIntegrationBundle\Domain\Entity;

class Address
{
    private string $formattedAddress;
    private string $addressString;
    private ?string $building;
    private string $postalCode;
    private string $town;
    private ?string $complement;

    private float $latitude;
    private float $longitude;

    public function __construct(
        string $formattedAddress,
        string $addressString,
        ?string $building,
        string $postalCode,
        string $town,
        ?string $complement,
        float $latitude,
        float $longitude
    ) {
        $this->formattedAddress = $formattedAddress;
        $this->addressString = $addressString;
        $this->building = $building;
        $this->postalCode = $postalCode;
        $this->town = $town;
        $this->complement = $complement;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getFormattedAddress(): string
    {
        return $this->formattedAddress;
    }

    public function getAddressString(): string
    {
        return $this->addressString;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getAdresseComplete(): string
    {
        $parts = [];

        if ($this->building) {
            $parts[] = $this->building;
        }

        if ($this->addressString) {
            $parts[] = $this->addressString;
        }

        if ($this->complement) {
            $parts[] = $this->complement;
        }

        // Ajoute le code postal + ville
        $parts[] = trim($this->postalCode . ' ' . $this->town);

        return implode(', ', array_filter($parts));
    }
}
