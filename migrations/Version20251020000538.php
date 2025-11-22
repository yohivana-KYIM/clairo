<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020000538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
-- --------------------------------------------------------------------
-- DEADLINES
-- --------------------------------------------------------------------

-- 1) REFSECU : ajoute fluxel_training_date (+ request_date pour homogénéité)
CREATE OR REPLACE VIEW v_deadlines_refsecu AS
SELECT
  step_id,
  company_name,
  employee_first_name,
  employee_last_name,
  employee_email,
  status,
  request_date,                -- <== ajouté
  contract_end_date,
  access_duration_step6,
  fluxel_training_date,        -- <== ajouté pour Twig: attribute(r,'fluxelTrainingDate')
  DATEDIFF(STR_TO_DATE(contract_end_date, '%Y-%m-%d'), CURDATE()) AS days_left_contract,
  DATEDIFF(
    DATE_ADD(STR_TO_DATE(request_date, '%Y-%m-%d'),
      INTERVAL CAST(REPLACE(access_duration_step6, ' mois', '') AS UNSIGNED) MONTH),
    CURDATE()
  ) AS days_left_access
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('approved', 'card_delivered');

-- 2) SDRI : conserve la logique "formation < 30 jours", ajoute quelques champs utiles
CREATE OR REPLACE VIEW v_deadlines_sdri AS
SELECT
  step_id,
  company_name,
  employee_first_name,
  employee_last_name,
  employee_email,
  status,
  request_date,                -- <== ajouté (cohérence/tri)
  contract_end_date,           -- <== ajouté (affichage éventuel)
  fluxel_training_date,
  DATEDIFF(
    DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH),
    CURDATE()
  ) AS days_left_training
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND fluxel_training_date IS NOT NULL
HAVING days_left_training <= 30;

-- 3) ADMIN : ajoute request_date pour référence/tri, conserve la détection d’expiration
CREATE OR REPLACE VIEW v_deadlines_admin AS
SELECT
  step_id,
  company_name,
  employee_first_name,
  employee_last_name,
  employee_email,
  status,
  request_date,                -- <== ajouté
  contract_end_date,
  fluxel_training_date,
  DATEDIFF(CURDATE(), STR_TO_DATE(contract_end_date, '%Y-%m-%d')) AS contract_expired_by,
  DATEDIFF(
    CURDATE(),
    DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH)
  ) AS training_expired_by
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND (
    DATEDIFF(CURDATE(), STR_TO_DATE(contract_end_date, '%Y-%m-%d')) > 0
    OR DATEDIFF(
         CURDATE(),
         DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH)
       ) > 0
  );
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_deadlines_refsecu AS
SELECT
  step_id,
  company_name,
  employee_first_name,
  employee_last_name,
  contract_end_date,
  access_duration_step6,
  DATEDIFF(STR_TO_DATE(contract_end_date, '%Y-%m-%d'), CURDATE()) AS days_left_contract,
  DATEDIFF(
    DATE_ADD(STR_TO_DATE(request_date, '%Y-%m-%d'),
    INTERVAL CAST(REPLACE(access_duration_step6, ' mois', '') AS UNSIGNED) MONTH),
    CURDATE()
  ) AS days_left_access
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('approved', 'card_delivered');
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_deadlines_sdri AS
SELECT
  step_id,
  company_name,
  fluxel_training_date,
  employee_email,
  DATEDIFF(
    DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH),
    CURDATE()
  ) AS days_left_training
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND fluxel_training_date IS NOT NULL
  HAVING days_left_training <= 30;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_deadlines_admin AS
SELECT
  step_id,
  company_name,
  employee_email,
  status,
  contract_end_date,
  fluxel_training_date,
  DATEDIFF(CURDATE(), STR_TO_DATE(contract_end_date, '%Y-%m-%d')) AS contract_expired_by,
  DATEDIFF(CURDATE(), DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH)) AS training_expired_by
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND (
    DATEDIFF(CURDATE(), STR_TO_DATE(contract_end_date, '%Y-%m-%d')) > 0
    OR DATEDIFF(CURDATE(), DATE_ADD(STR_TO_DATE(fluxel_training_date, '%Y-%m-%d'), INTERVAL 12 MONTH)) > 0
  );
SQL);
    }
}
