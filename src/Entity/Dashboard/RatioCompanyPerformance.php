<?php

namespace App\Entity\Dashboard;


use App\Repository\Dashboard\RatioCompanyPerformanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatioCompanyPerformanceRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ratio_company_performance')]
class RatioCompanyPerformance
{
    #[ORM\Id]
	#[ORM\Column(name: 'company_name', type: TYPES::STRING, length: 255)]
	private string $companyName;

    #[ORM\Column(name: 'total_requests', type: TYPES::INTEGER)]
	private ?int $totalRequests = 0;
    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $approved = 0;
    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $refused = 0;

    #[ORM\Column(name: 'approval_rate', type: Types::FLOAT)]
	private ?float $approvalRate = 0.0;
    #[ORM\Column(name: 'refusal_rate', type: Types::FLOAT)]
	private ?float $refusalRate = 0.0;


    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setTotalRequests(int $totalRequests): void
    {
        $this->totalRequests = $totalRequests;
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

    public function setRefusalRate(float $refusalRate): void
    {
        $this->refusalRate = $refusalRate;
    }

    public function getCompanyName(): string { return $this->companyName ?? ''; }
    public function getTotalRequests(): int { return $this->totalRequests ?? 0; }
    public function getApproved(): int { return $this->approved ?? 0; }
    public function getRefused(): int { return $this->refused ?? 0; }
    public function getApprovalRate(): float { return $this->approvalRate ?? 0.0; }
    public function getRefusalRate(): float { return $this->refusalRate ?? 0.0; }
}
