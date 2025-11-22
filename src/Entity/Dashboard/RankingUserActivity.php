<?php

namespace App\Entity\Dashboard;

use App\Repository\Dashboard\RankingUserActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingUserActivityRepository::class, readOnly: true)]
#[ORM\Table(name: 'v_ranking_user_activity')]
class RankingUserActivity
{
    #[ORM\Id]
	#[ORM\Column(name: 'employee_email', type: TYPES::STRING, length: 255)]
	private string $employeeEmail;
    #[ORM\Column(name: 'total_submitted', type: TYPES::INTEGER)]
	private ?int $totalSubmitted = 0;

    public function getEmployeeEmail(): string { return $this->employeeEmail ?? ''; }

    public function setEmployeeEmail(string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
    }

    public function getTotalSubmitted(): int { return $this->totalSubmitted ?? 0; }

    public function setTotalSubmitted(int $totalSubmitted): void
    {
        $this->totalSubmitted = $totalSubmitted;
    }
}
