<?php
// =====================================================================
// FILE: src/Repository/Dashboard/DeadlinesAdminRepository.php
// =====================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DeadlinesAdmin;
use Doctrine\Persistence\ManagerRegistry;

class DeadlinesAdminRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeadlinesAdmin::class);
    }
}
