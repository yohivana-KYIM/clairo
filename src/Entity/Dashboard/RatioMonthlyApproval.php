<?php
// ======================================================
// FILE: src/Entity/Dashboard/RatioMonthlyApproval.php
// ======================================================

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RatioMonthlyApprovalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatioMonthlyApprovalRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ratio_monthly_approval')]
class RatioMonthlyApproval
{
    #[ORM\Id]
    #[ORM\Column(name: 'month', type: TYPES::STRING, length: 7)]
    private string $month; // YYYY-MM

    #[ORM\Column(name: 'total', type: TYPES::INTEGER)]
    private ?int $total = 0;

    #[ORM\Column(name: 'approved', type: TYPES::INTEGER)]
    private ?int $approved = 0;

    #[ORM\Column(name: 'refused', type: TYPES::INTEGER)]
    private ?int $refused = 0;

    #[ORM\Column(name: 'approval_rate', type: Types::FLOAT)]
    private ?float $approvalRate = 0.0;

    public function getMonth(): string { return $this->month ?? ''; }
    public function getTotal(): int { return $this->total ?? 0; }
    public function getApproved(): int { return $this->approved ?? 0; }
    public function getRefused(): int { return $this->refused ?? 0; }
    public function getApprovalRate(): float { return $this->approvalRate ?? 0.0; }

    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function setApproved(int $approved): void
    {
        $this->approved = $approved;
    }

    public function setRefused(int $refused): void
    {
        $this->refused = $refused;
    }

    public function setApprovalRate(float $approvalRate): void
    {
        $this->approvalRate = $approvalRate;
    }
}
