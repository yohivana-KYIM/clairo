<?php
// ======================================================================
// FILE: src/Repository/Dashboard/RatioMonthlyApprovalRepository.php
// ======================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RatioMonthlyApproval;
use Doctrine\Persistence\ManagerRegistry;

class RatioMonthlyApprovalRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatioMonthlyApproval::class);
    }
}
