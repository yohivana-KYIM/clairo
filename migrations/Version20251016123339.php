<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251016123339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Recreate SQL views for CLEO dashboard chart';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE OR REPLACE VIEW v_chart_decision_evolution AS
SELECT
  DATE_FORMAT(STR_TO_DATE(request_date, '%Y-%m-%d'), '%Y-%m') AS month,
  status as access_decision,
  COUNT(*) AS count
FROM v_person_flattened_step_data
WHERE step_type = 'person'
and status in ('approved', 'refused', 'provisioned', 'enquete_prealable', 'pending')
GROUP BY month, status
ORDER BY month ASC;
SQL);

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP VIEW IF EXISTS v_chart_decision_evolution;");
    }
}
