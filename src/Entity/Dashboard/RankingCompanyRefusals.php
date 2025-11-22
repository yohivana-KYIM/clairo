<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RankingCompanyRefusalsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingCompanyRefusalsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ranking_company_refusals')]
class RankingCompanyRefusals
{
    #[ORM\Id]
	#[ORM\Column(name: 'company_name', type: TYPES::STRING, length: 255)]
	private string $companyName;
    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $refusals = 0;

    public function getCompanyName(): string { return $this->companyName ?? ''; }
    public function getRefusals(): int { return $this->refusals ?? 0; }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setRefusals(int $refusals): void
    {
        $this->refusals = $refusals;
    }
}
