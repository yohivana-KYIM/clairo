<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RankingCompanyRequestsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingCompanyRequestsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ranking_company_requests')]
class RankingCompanyRequests
{
    #[ORM\Id]
	#[ORM\Column(name: 'company_name', type: TYPES::STRING, length: 255)]
	private string $companyName;
    #[ORM\Column(name: 'total_requests', type: TYPES::INTEGER)]
	private ?int $totalRequests = 0;

    public function getCompanyName(): string { return $this->companyName ?? ''; }
    public function getTotalRequests(): int { return $this->totalRequests ?? 0; }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setTotalRequests(int $totalRequests): void
    {
        $this->totalRequests = $totalRequests;
    }
}
