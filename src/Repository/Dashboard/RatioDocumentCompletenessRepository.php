<?php
// ==================================================================================
// FILE: src/Repository/Dashboard/RatioDocumentCompletenessRepository.php
// ==================================================================================

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RatioDocumentCompleteness;
use Doctrine\Persistence\ManagerRegistry;

class RatioDocumentCompletenessRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatioDocumentCompleteness::class);
    }
}
