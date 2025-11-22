<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251017175614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE OR REPLACE VIEW v_alerts_user AS
        SELECT
          step_id,
          company_name,
          employee_email,
          status,
          request_date,
          'Documents manquants' AS alert_type
        FROM v_person_flattened_step_data
        WHERE step_type = 'person'
          AND (gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL)
          AND status IN ('draft','awaiting_info');
SQL);

    }

    public function down(Schema $schema): void
    {
        $views = [
            'company_name',
        ];

        foreach ($views as $view) {
            $this->addSql("DROP VIEW IF EXISTS `$view`;");
        }
    }
}
