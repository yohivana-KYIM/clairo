<?php
// ========================================================================
// FILE: src/Repository/Dashboard/GardienPersonStepsRepository.php
// ========================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\GardienPersonSteps;
use Doctrine\Persistence\ManagerRegistry;

class GardienPersonStepsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GardienPersonSteps::class);
    }
}
