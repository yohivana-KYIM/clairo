<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RankingCompanyMissingDocsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingCompanyMissingDocsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ranking_company_missing_docs')]
class RankingCompanyMissingDocs
{
    #[ORM\Id]
	#[ORM\Column(name: 'company_name', type: TYPES::STRING, length: 255)]
	private string $companyName;
    #[ORM\Column(name: 'incomplete_requests', type: Types::INTEGER)]
	private ?int $incompleteRequests = 0;

    public function getCompanyName(): string { return $this->companyName ?? ''; }
    public function getIncompleteRequests(): int { return $this->incompleteRequests ?? 0; }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setIncompleteRequests(int $incompleteRequests): void
    {
        $this->incompleteRequests = $incompleteRequests;
    }
}
