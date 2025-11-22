<?php
// =====================================================================
// FILE: src/Repository/Dashboard/AlertsRefsecuRepository.php
// =====================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\AlertsRefsecu;
use Doctrine\Persistence\ManagerRegistry;

class AlertsRefsecuRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertsRefsecu::class);
    }
}
