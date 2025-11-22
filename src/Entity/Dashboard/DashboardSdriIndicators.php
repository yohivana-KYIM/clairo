<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\DashboardSdriIndicatorsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardSdriIndicatorsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_dashboard_sdri_indicators')]
class DashboardSdriIndicators
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

    #[ORM\Column(name: 'to_review', type: Types::INTEGER)]
	private ?int $toReview = 0;

    #[ORM\Column(name: 'needs_info', type: Types::INTEGER)]
	private ?int $needsInfo = 0;

    #[ORM\Column(name: 'under_investigation', type: Types::INTEGER)]
	private ?int $underInvestigation = 0;

    #[ORM\Column(name: 'tech_in_progress', type: Types::INTEGER)]
	private ?int $techInProgress = 0;

    #[ORM\Column(name: 'cerbere_sync', type: Types::INTEGER)]
	private ?int $cerbereSync = 0;

    #[ORM\Column(name: 'refused_total', type: Types::INTEGER)]
	private ?int $refusedTotal = 0;

    /**
     * @return int
     */
    public function getToReview(): int { return $this->toReview ?? 0; }

    /**
     * @param int $toReview
     */
    public function setToReview(int $toReview): void
    {
        $this->toReview = $toReview;
    }

    /**
     * @return int
     */
    public function getNeedsInfo(): int { return $this->needsInfo ?? 0; }

    /**
     * @param int $needsInfo
     */
    public function setNeedsInfo(int $needsInfo): void
    {
        $this->needsInfo = $needsInfo;
    }

    /**
     * @return int
     */
    public function getUnderInvestigation(): int { return $this->underInvestigation ?? 0; }

    /**
     * @param int $underInvestigation
     */
    public function setUnderInvestigation(int $underInvestigation): void
    {
        $this->underInvestigation = $underInvestigation;
    }

    /**
     * @return int
     */
    public function getTechInProgress(): int { return $this->techInProgress ?? 0; }

    /**
     * @param int $techInProgress
     */
    public function setTechInProgress(int $techInProgress): void
    {
        $this->techInProgress = $techInProgress;
    }

    /**
     * @return int
     */
    public function getCerbereSync(): int { return $this->cerbereSync ?? 0; }

    /**
     * @param int $cerbereSync
     */
    public function setCerbereSync(int $cerbereSync): void
    {
        $this->cerbereSync = $cerbereSync;
    }

    /**
     * @return int
     */
    public function getRefusedTotal(): int { return $this->refusedTotal ?? 0; }

    /**
     * @param int $refusedTotal
     */
    public function setRefusedTotal(int $refusedTotal): void
    {
        $this->refusedTotal = $refusedTotal;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
