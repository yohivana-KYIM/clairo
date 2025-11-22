<?php
// ========================================================================
// FILE: src/Repository/Dashboard/PersonRefsecuStepsRepository.php
// ========================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\PersonRefsecuSteps;
use Doctrine\Persistence\ManagerRegistry;

class PersonRefsecuStepsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonRefsecuSteps::class);
    }
}
