<?php
// ==================================================================
// FILE: src/Repository/Dashboard/PersonUserStepsRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\PersonUserSteps;
use Doctrine\Persistence\ManagerRegistry;

class PersonUserStepsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonUserSteps::class);
    }
}
