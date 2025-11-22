<?php

namespace App\Entity\Dashboard;
use App\Repository\Dashboard\DashboardAdminIndicatorsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardAdminIndicatorsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_dashboard_admin_indicators')]
class DashboardAdminIndicators
{

    // 🧩 Synthetic ID added automatically
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

    #[ORM\Column(name: 'total_requests', type: TYPES::INTEGER)]
	private ?int $totalRequests = 0;

    #[ORM\Column(name: 'total_companies', type: TYPES::INTEGER)]
	private ?int $totalCompanies = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $drafts = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $approved = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $refused = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $delivered = 0;

    #[ORM\Column(name: 'in_process', type: TYPES::INTEGER)]
	private ?int $inProcess = 0;

    public function getTotalRequests(): int { return $this->totalRequests ?? 0; }
    public function getTotalCompanies(): int { return $this->totalCompanies ?? 0; }
    public function getDrafts(): int { return $this->drafts ?? 0; }
    public function getApproved(): int { return $this->approved ?? 0; }
    public function getRefused(): int { return $this->refused ?? 0; }
    public function getDelivered(): int { return $this->delivered ?? 0; }
    public function getInProcess(): int { return $this->inProcess ?? 0; }

    public function setTotalRequests(int $totalRequests): void
    {
        $this->totalRequests = $totalRequests;
    }

    public function setTotalCompanies(int $totalCompanies): void
    {
        $this->totalCompanies = $totalCompanies;
    }

    public function setDrafts(int $drafts): void
    {
        $this->drafts = $drafts;
    }

    public function setApproved(int $approved): void
    {
        $this->approved = $approved;
    }

    public function setRefused(int $refused): void
    {
        $this->refused = $refused;
    }

    public function setDelivered(int $delivered): void
    {
        $this->delivered = $delivered;
    }

    public function setInProcess(int $inProcess): void
    {
        $this->inProcess = $inProcess;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
