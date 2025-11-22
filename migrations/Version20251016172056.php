<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251016172056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'SQL views for dashboards';
    }

    public function up(Schema $schema): void
    {
        // --------------------------------------------------------------------
        // RANKINGS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ranking_sdri_validations AS
SELECT
  security_officer_name,
  COUNT(*) AS approved_count
FROM v_person_flattened_step_data
WHERE status = 'approved'
  AND step_type = 'person'
GROUP BY security_officer_name
ORDER BY approved_count DESC
LIMIT 20;
SQL);

        // --------------------------------------------------------------------
        // ALERTS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_alerts_refsecu AS
SELECT
  step_id,
  company_name,
  status,
  request_date,
  CASE
    WHEN status = 'awaiting_reference' THEN 'Référencement en attente'
    WHEN status = 'paid' THEN 'Carte à éditer'
    WHEN status = 'card_edited' THEN 'Carte à remettre'
    ELSE 'Suivi nécessaire'
  END AS alert_type
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('awaiting_reference', 'paid', 'card_edited');
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_alerts_sdri AS
SELECT
  step_id,
  company_name,
  status,
  request_date,
  CASE
    WHEN status = 'enquete_prealable' THEN 'Enquête en attente'
    WHEN status = 'microcesame_ko' THEN 'Échec Microcésame'
    WHEN status = 'cerbere_ko' THEN 'Échec Cerbère'
    ELSE 'Blocage technique'
  END AS alert_type
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('enquete_prealable', 'microcesame_ko', 'cerbere_ko');
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_alerts_admin AS
SELECT
  step_id,
  company_name,
  status,
  request_date,
  DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) AS days_open,
  'Dossier en attente critique' AS alert_type
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status NOT IN ('card_delivered', 'refused')
  AND DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 10;
SQL);

        // --------------------------------------------------------------------
        // DEADLINES
        // --------------------------------------------------------------------
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

        // --------------------------------------------------------------------
        // TO-DO
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_user AS
SELECT
  step_id,
  status,
  employee_email,
  'Compléter les documents manquants' AS todo_type,
  CASE
    WHEN DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 15 THEN 'urgent'
    WHEN status IN ('awaiting_info', 'pending') THEN 'à traiter rapidement'
    ELSE 'normal'
  END AS priority_level
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND employee_email IS NOT NULL
  AND (
    status IN ('draft', 'awaiting_info')
    OR gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL
  );
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_refsecu AS
SELECT
  step_id,
  company_name,
  status,
  CASE
    WHEN status = 'awaiting_reference' THEN 'Compléter le référencement'
    WHEN status = 'paid' THEN 'Éditer la carte'
    WHEN status = 'card_edited' THEN 'Remettre la carte'
    WHEN status = 'payment_doc_ko' THEN 'Corriger la facture'
    ELSE 'Suivre le dossier'
  END AS todo_type,
  CASE
    WHEN DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 15 THEN 'urgent'
    WHEN status IN ('awaiting_info', 'pending') THEN 'à traiter rapidement'
    ELSE 'normal'
  END AS priority_level
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('awaiting_reference', 'paid', 'card_edited', 'payment_doc_ko');
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_sdri AS
SELECT
  step_id,
  company_name,
  status,
  CASE
    WHEN status = 'pending' THEN 'Analyser la demande'
    WHEN status = 'awaiting_info' THEN 'Traiter les infos complémentaires'
    WHEN status = 'microcesame_ko' THEN 'Corriger Microcésame'
    WHEN status = 'cerbere_ko' THEN 'Relancer Cerbère'
    WHEN status = 'enquete_prealable' THEN 'Valider l\'enquête'
    ELSE 'Suivi technique requis'
  END AS todo_type,
  CASE
    WHEN DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 15 THEN 'urgent'
    WHEN status IN ('awaiting_info', 'pending') THEN 'à traiter rapidement'
    ELSE 'normal'
  END AS priority_level
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('pending', 'awaiting_info', 'microcesame_ko', 'cerbere_ko', 'enquete_prealable');
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_admin AS
SELECT
  step_id,
  company_name,
  status,
  request_date,
  'Audit de dossier en retard ou bloqué' AS todo_type,
  CASE
    WHEN DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 15 THEN 'urgent'
    WHEN status IN ('awaiting_info', 'pending') THEN 'à traiter rapidement'
    ELSE 'normal'
  END AS priority_level
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status NOT IN ('card_delivered', 'refused')
  AND DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) > 15;
SQL);

        // --------------------------------------------------------------------
        // RECENT / PRIORITY
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_recent_user_requests AS
SELECT
  step_id,
  employee_email,
  status,
  request_date,
  DATEDIFF(NOW(), STR_TO_DATE(request_date, '%Y-%m-%d')) AS days_since,
  CASE
    WHEN status = 'awaiting_info' THEN '🔴 Besoin d’action'
    WHEN status = 'approved' THEN '🟢 Approuvée'
    WHEN status = 'draft' THEN '⚪ En cours de rédaction'
    ELSE '🟠 Suivi'
  END AS priority
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND STR_TO_DATE(request_date, '%Y-%m-%d') >= DATE_SUB(NOW(), INTERVAL 15 DAY);
SQL);

        // --------------------------------------------------------------------
        // RATIOS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ratio_company_performance AS
SELECT
  company_name,
  COUNT(*) AS total_requests,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
  ROUND(100 * SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) / COUNT(*), 2) AS approval_rate,
  ROUND(100 * SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) / COUNT(*), 2) AS refusal_rate
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY company_name
ORDER BY approval_rate DESC;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ratio_user_activity AS
SELECT
  employee_email,
  COUNT(*) AS total,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
  ROUND(100 * SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) / COUNT(*), 2) AS approval_rate
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY employee_email
HAVING total >= 5
ORDER BY approval_rate DESC;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ratio_document_completeness AS
SELECT
  company_name,
  COUNT(*) AS total_requests,
  SUM(CASE WHEN gies_1 IS NOT NULL AND atex_0 IS NOT NULL AND zar IS NOT NULL AND signature IS NOT NULL THEN 1 ELSE 0 END) AS complete_requests,
  ROUND(100 * SUM(CASE WHEN gies_1 IS NOT NULL AND atex_0 IS NOT NULL AND zar IS NOT NULL AND signature IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*), 2) AS completeness_rate
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY company_name
ORDER BY completeness_rate DESC;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ratio_monthly_approval AS
SELECT
  DATE_FORMAT(STR_TO_DATE(request_date, '%Y-%m-%d'), '%Y-%m') AS month,
  COUNT(*) AS total,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
  ROUND(100 * SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) / COUNT(*), 2) AS approval_rate
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY month
ORDER BY month;
SQL);
    }

    public function down(Schema $schema): void
    {
        $views = [
            'v_ranking_sdri_validations',
            'v_alerts_refsecu',
            'v_alerts_sdri',
            'v_alerts_admin',
            'v_deadlines_refsecu',
            'v_deadlines_sdri',
            'v_deadlines_admin',
            'v_todo_user',
            'v_todo_refsecu',
            'v_todo_sdri',
            'v_todo_admin',
            'v_recent_user_requests',
            'v_ratio_company_performance',
            'v_ratio_user_activity',
            'v_ratio_document_completeness',
            'v_ratio_monthly_approval'
        ];

        foreach ($views as $view) {
            $this->addSql("DROP VIEW IF EXISTS `$view`;");
        }
    }
}
