<?php
// ==========================================================
// FILE: src/Entity/Dashboard/RankingSdriValidations.php
// ==========================================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_ranking_sdri_validations')]
class RankingSdriValidations
{
    #[ORM\Id]
    #[ORM\Column(name: 'security_officer_name', type: TYPES::TEXT)]
    private string $securityOfficerName;

    #[ORM\Column(name: 'approved_count', type: TYPES::INTEGER)]
    private ?int $approvedCount = 0;

    public function getSecurityOfficerName(): string { return $this->securityOfficerName ?? ''; }
    public function getApprovedCount(): int { return $this->approvedCount ?? 0; }

    public function setSecurityOfficerName(string $securityOfficerName): void
    {
        $this->securityOfficerName = $securityOfficerName;
    }

    public function setApprovedCount(int $approvedCount): void
    {
        $this->approvedCount = $approvedCount;
    }
}
