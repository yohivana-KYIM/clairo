<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019233158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_user AS
SELECT
  step_id,
  company_name,
  status,
  employee_email,
  /* Normalise la date en DATE si elle est stockée en texte YYYY-MM-DD */
  CASE
    WHEN request_date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
      THEN STR_TO_DATE(request_date, '%Y-%m-%d')
    ELSE NULL
  END AS request_date,
  /* Intitulé de la todo */
  'Compléter les documents manquants' AS todo_type,
  /* Priorité : >15j d’ancienneté => urgent ; sinon en attente d'infos/pending => à traiter rapidement ; sinon normal */
  CASE
    WHEN DATEDIFF(CURDATE(),
                  CASE
                    WHEN request_date REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'
                      THEN STR_TO_DATE(request_date, '%Y-%m-%d')
                    ELSE CURDATE()
                  END
         ) > 15
      THEN 'urgent'
    WHEN status IN ('awaiting_info','pending')
      THEN 'à traiter rapidement'
    ELSE 'normal'
  END AS priority_level
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND employee_email IS NOT NULL
  AND (
    status IN ('draft','awaiting_info','pending')
    OR gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL
  );
SQL);
    }

    public function down(Schema $schema): void
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
}
