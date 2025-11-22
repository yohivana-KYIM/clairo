<?php
// ===================================================
// FILE: src/Entity/Dashboard/RatioUserActivity.php
// ===================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_ratio_user_activity')]
class RatioUserActivity
{
    #[ORM\Id]
    #[ORM\Column(name: 'employee_email', type: TYPES::TEXT)]
    private string $employeeEmail;

    #[ORM\Column(name: 'total', type: TYPES::INTEGER)]
    private ?int $total = 0;

    #[ORM\Column(name: 'approved', type: TYPES::INTEGER)]
    private ?int $approved = 0;

    #[ORM\Column(name: 'refused', type: TYPES::INTEGER)]
    private ?int $refused = 0;

    #[ORM\Column(name: 'approval_rate', type: Types::FLOAT)]
    private ?float $approvalRate = 0.0;

    public function getEmployeeEmail(): string { return $this->employeeEmail ?? ''; }
    public function getTotal(): int { return $this->total ?? 0; }
    public function getApproved(): int { return $this->approved ?? 0; }
    public function getRefused(): int { return $this->refused ?? 0; }
    public function getApprovalRate(): float { return $this->approvalRate ?? 0.0; }

    public function setEmployeeEmail(string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
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
