<?php
// ==================================================================
// FILE: src/Repository/Dashboard/AlertsAdminRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\AlertsAdmin;
use Doctrine\Persistence\ManagerRegistry;

class AlertsAdminRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertsAdmin::class);
    }
}
