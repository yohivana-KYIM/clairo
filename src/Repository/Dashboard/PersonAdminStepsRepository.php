<?php
// ======================================================================
// FILE: src/Repository/Dashboard/PersonAdminStepsRepository.php
// ======================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\PersonAdminSteps;
use Doctrine\Persistence\ManagerRegistry;

class PersonAdminStepsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonAdminSteps::class);
    }
}
