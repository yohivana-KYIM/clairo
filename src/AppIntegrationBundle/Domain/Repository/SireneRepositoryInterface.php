<?php

namespace App\AppIntegrationBundle\Domain\Repository;


use App\AppIntegrationBundle\Domain\Entity\Company;

interface SireneRepositoryInterface
{
    public function findBySiren(string $siren): ?Company;
}
