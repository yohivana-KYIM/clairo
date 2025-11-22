<?php
// ==================================================================
// FILE: src/Repository/Dashboard/DeadlinesSdriRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DeadlinesSdri;
use Doctrine\Persistence\ManagerRegistry;

class DeadlinesSdriRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeadlinesSdri::class);
    }
}
