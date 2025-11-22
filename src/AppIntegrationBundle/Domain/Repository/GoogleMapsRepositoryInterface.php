<?php

namespace App\AppIntegrationBundle\Domain\Repository;

use App\AppIntegrationBundle\Domain\Entity\Address;

interface GoogleMapsRepositoryInterface
{
    public function getGeolocation(string $address): ?Address;
}
