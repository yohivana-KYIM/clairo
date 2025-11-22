<?php
// ==================================================================
// FILE: src/Repository/Dashboard/PersonSdriStepsRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\PersonSdriSteps;
use Doctrine\Persistence\ManagerRegistry;

class PersonSdriStepsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonSdriSteps::class);
    }
}
