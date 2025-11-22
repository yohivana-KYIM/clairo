<?php
// =============================================
// FILE: src/Entity/Dashboard/AlertsAdmin.php
// =============================================

namespace App\Entity\Dashboard;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_alerts_admin')]
class AlertsAdmin
{
    #[ORM\Id]
    #[ORM\Column(name: 'step_id', type: TYPES::INTEGER)]
    private ?int $stepId = 0;

    #[ORM\Column(name: 'company_name', type: TYPES::TEXT, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(name: 'status', type: TYPES::STRING, length: 255)]
    private string $status;

    #[ORM\Column(name: 'request_date', type: TYPES::TEXT, nullable: true)]
    private ?string $requestDate = null;

    #[ORM\Column(name: 'days_open', type: TYPES::INTEGER)]
    private ?int $daysOpen = 0;

    #[ORM\Column(name: 'alert_type', type: TYPES::STRING, length: 64)]
    #[ORM\Cache(usage: 'READ_ONLY', region: 'reference_data')]
    private string $alertType;

    public function getStepId(): int { return $this->stepId ?? 0; }
    public function getCompanyName(): ?string {
		 return $this->companyName;
	}
    public function getStatus(): string { return $this->status ?? ''; }
    public function getRequestDate(): ?string {
		 return $this->requestDate;
	}
    public function getDaysOpen(): int { return $this->daysOpen ?? 0; }
    public function getAlertType(): string { return $this->alertType ?? ''; }

    public function setStepId(int $stepId): void
    {
        $this->stepId = $stepId;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setDaysOpen(int $daysOpen): void
    {
        $this->daysOpen = $daysOpen;
    }

    public function setAlertType(string $alertType): void
    {
        $this->alertType = $alertType;
    }
}
