<?php
// =======================================================================
// FILE: src/Repository/Dashboard/RatioUserActivityRepository.php
// =======================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RatioUserActivity;
use Doctrine\Persistence\ManagerRegistry;

class RatioUserActivityRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatioUserActivity::class);
    }
}
