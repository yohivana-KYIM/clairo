<?php
declare(strict_types=1);

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\DashboardRefsecuIndicatorsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DashboardRefsecuIndicatorsRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_dashboard_refsecu_indicators')]
class DashboardRefsecuIndicators
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

    #[ORM\Column(name: 'to_reference', type: TYPES::INTEGER)]
	private ?int $toReference = 0;

    #[ORM\Column(name: 'ready_for_tech_phase', type: TYPES::INTEGER)]
	private ?int $readyForTechPhase = 0;

    #[ORM\Column(name: 'to_invoice', type: TYPES::INTEGER)]
	private ?int $toInvoice = 0;

    #[ORM\Column(name: 'awaiting_payment', type: TYPES::INTEGER)]
	private ?int $awaitingPayment = 0;

    #[ORM\Column(name: 'to_edit_card', type: TYPES::INTEGER)]
	private ?int $toEditCard = 0;

    #[ORM\Column(name: 'to_deliver', type: TYPES::INTEGER)]
	private ?int $toDeliver = 0;

    #[ORM\Column(type: TYPES::INTEGER)]
	private ?int $undecided = 0;

    public function getToReference(): int { return $this->toReference ?? 0; }
    public function getReadyForTechPhase(): int { return $this->readyForTechPhase ?? 0; }
    public function getToInvoice(): int { return $this->toInvoice ?? 0; }
    public function getAwaitingPayment(): int { return $this->awaitingPayment ?? 0; }
    public function getToEditCard(): int { return $this->toEditCard ?? 0; }
    public function getToDeliver(): int { return $this->toDeliver ?? 0; }
    public function getUndecided(): int { return $this->undecided ?? 0; }

    public function setToReference(int $toReference): void
    {
        $this->toReference = $toReference;
    }

    public function setReadyForTechPhase(int $readyForTechPhase): DashboardRefsecuIndicators
    {
        $this->readyForTechPhase = $readyForTechPhase;
        return $this;
    }

    public function setToInvoice(int $toInvoice): void
    {
        $this->toInvoice = $toInvoice;
    }

    public function setAwaitingPayment(int $awaitingPayment): void
    {
        $this->awaitingPayment = $awaitingPayment;
    }

    public function setToEditCard(int $toEditCard): void
    {
        $this->toEditCard = $toEditCard;
    }

    public function setToDeliver(int $toDeliver): void
    {
        $this->toDeliver = $toDeliver;
    }

    public function setUndecided(int $undecided): void
    {
        $this->undecided = $undecided;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
