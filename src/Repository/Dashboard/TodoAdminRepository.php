<?php
// ==================================================================
// FILE: src/Repository/Dashboard/TodoAdminRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\TodoAdmin;
use Doctrine\Persistence\ManagerRegistry;

class TodoAdminRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoAdmin::class);
    }
}
