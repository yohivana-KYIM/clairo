<?php
// ===============================================================
// FILE: src/Entity/Dashboard/RatioDocumentCompleteness.php
// ===============================================================

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RatioDocumentCompletenessRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatioDocumentCompletenessRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ratio_document_completeness')]
class RatioDocumentCompleteness
{
    #[ORM\Id]
    #[ORM\Column(name: 'company_name', type: TYPES::TEXT)]
    private string $companyName;

    #[ORM\Column(name: 'total_requests', type: TYPES::INTEGER)]
    private ?int $totalRequests = 0;

    #[ORM\Column(name: 'complete_requests', type: TYPES::INTEGER)]
    private ?int $completeRequests = 0;

    #[ORM\Column(name: 'completeness_rate', type: Types::FLOAT)]
    private ?float $completenessRate = 0.0;

    public function getCompanyName(): string { return $this->companyName ?? ''; }
    public function getTotalRequests(): int { return $this->totalRequests ?? 0; }
    public function getCompleteRequests(): int { return $this->completeRequests ?? 0; }
    public function getCompletenessRate(): float { return $this->completenessRate ?? 0.0; }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setTotalRequests(int $totalRequests): void
    {
        $this->totalRequests = $totalRequests;
    }

    public function setCompleteRequests(int $completeRequests): void
    {
        $this->completeRequests = $completeRequests;
    }

    public function setCompletenessRate(float $completenessRate): void
    {
        $this->completenessRate = $completenessRate;
    }
}
