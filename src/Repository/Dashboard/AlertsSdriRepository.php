<?php
// ==================================================================
// FILE: src/Repository/Dashboard/AlertsSdriRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\AlertsSdri;
use Doctrine\Persistence\ManagerRegistry;

class AlertsSdriRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertsSdri::class);
    }
}
