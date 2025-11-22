<?php
// ==================================================================
// FILE: src/Repository/Dashboard/TodoSdriRepository.php
// ==================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\TodoSdri;
use Doctrine\Persistence\ManagerRegistry;

class TodoSdriRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoSdri::class);
    }
}