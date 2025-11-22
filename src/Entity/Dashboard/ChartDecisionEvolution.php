<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\ChartDecisionEvolutionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChartDecisionEvolutionRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_chart_decision_evolution')]
class ChartDecisionEvolution
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $id;

    public function __construct()
    {
        // Generate synthetic ID on construction
        $this->computeSyntheticId();
    }

    private function computeSyntheticId(): void
    {
        // Build an MD5 hash of all object vars
        $this->id = md5(json_encode(get_object_vars($this)));
    }

    public function getId(): string { return $this->id ?? ''; }

    #[ORM\Column(type: TYPES::STRING, length: 7)]
	private string $month; // YYYY-MM

    #[ORM\Column(name: 'access_decision', type: TYPES::STRING, length: 64)]
	private string $accessDecision;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $count = 0;

    public function getMonth(): string { return $this->month ?? ''; }

    public function getAccessDecision(): string { return $this->accessDecision ?? ''; }

    public function getCount(): int { return $this->count ?? 0; }

    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    public function setAccessDecision(string $accessDecision): void
    {
        $this->accessDecision = $accessDecision;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
