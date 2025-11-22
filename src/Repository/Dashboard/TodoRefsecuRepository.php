<?php
// =====================================================================
// FILE: src/Repository/Dashboard/TodoRefsecuRepository.php
// =====================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\TodoRefsecu;
use Doctrine\Persistence\ManagerRegistry;

class TodoRefsecuRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoRefsecu::class);
    }
}
