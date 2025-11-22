<?php
declare(strict_types=1);

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\DashboardUserIndicatorsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardUserIndicatorsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_dashboard_user_indicators')]
class DashboardUserIndicators
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
    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $drafts = 0;

    #[ORM\Column(name: 'in_progress', type: TYPES::INTEGER)]
	private ?int $inProgress = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $approved = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $refused = 0;

    #[ORM\Column(name: 'missing_documents', type: TYPES::INTEGER)]
	private ?int $missingDocuments = 0;

    public function getDrafts(): int { return $this->drafts ?? 0; }
    public function getInProgress(): int { return $this->inProgress ?? 0; }
    public function getApproved(): int { return $this->approved ?? 0; }
    public function getRefused(): int { return $this->refused ?? 0; }
    public function getMissingDocuments(): int { return $this->missingDocuments ?? 0; }

    public function setDrafts(int $drafts): void
    {
        $this->drafts = $drafts;
    }

    public function setInProgress(int $inProgress): void
    {
        $this->inProgress = $inProgress;
    }

    public function setApproved(int $approved): void
    {
        $this->approved = $approved;
    }

    public function setRefused(int $refused): void
    {
        $this->refused = $refused;
    }

    public function setMissingDocuments(int $missingDocuments): void
    {
        $this->missingDocuments = $missingDocuments;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
