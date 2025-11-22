<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\ChartRequestsMonthlyStatusRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: ChartRequestsMonthlyStatusRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_chart_requests_monthly_status')]
class ChartRequestsMonthlyStatus
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

    #[ORM\Column(type: TYPES::STRING, length: 64)]
	private string $status;
    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $count = 0;

    public function getMonth(): string { return $this->month ?? ''; }

    public function setMonth(string $month): void
    {
        $this->month = $month;
    }

    public function getStatus(): string { return $this->status ?? ''; }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCount(): int { return $this->count ?? 0; }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
