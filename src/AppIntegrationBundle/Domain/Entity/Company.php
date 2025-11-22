<?php

namespace App\AppIntegrationBundle\Domain\Entity;

class Company
{
    private string $siren;
    private string $name;
    private ?string $address;
    private ?string $activityCode;

    public function __construct(string $siren, string $name, ?string $address = null, ?string $activityCode = null)
    {
        $this->siren = $siren;
        $this->name = $name;
        $this->address = $address;
        $this->activityCode = $activityCode;
    }

    public function getSiren(): string
    {
        return $this->siren;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getActivityCode(): ?string
    {
        return $this->activityCode;
    }
}
