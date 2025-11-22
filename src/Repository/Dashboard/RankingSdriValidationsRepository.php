<?php
// =================================================================================
// FILE: src/Repository/Dashboard/RankingSdriValidationsRepository.php
// =================================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RankingSdriValidations;
use Doctrine\Persistence\ManagerRegistry;

class RankingSdriValidationsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingSdriValidations::class);
    }
}
