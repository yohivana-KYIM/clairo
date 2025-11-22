<?php
// =======================================================================
// FILE: src/Repository/Dashboard/DeadlinesRefsecuRepository.php
// =======================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DeadlinesRefsecu;
use Doctrine\Persistence\ManagerRegistry;

class DeadlinesRefsecuRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeadlinesRefsecu::class);
    }
}
