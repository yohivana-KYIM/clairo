<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251014094858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create all SQL views for CLEO dashboards (indicators, charts, rankings, alerts, deadlines, to-dos, recent, ratios)';
    }

    public function up(Schema $schema): void
    {
        // --------------------------------------------------------------------
        // INDICATORS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_dashboard_user_indicators AS
SELECT
  SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS drafts,
  SUM(CASE WHEN status IN ('awaiting_reference', 'pending', 'awaiting_info') THEN 1 ELSE 0 END) AS in_progress,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
  SUM(CASE
    WHEN gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL
    THEN 1 ELSE 0 END
  ) AS missing_documents
FROM v_person_flattened_step_data
WHERE step_type = 'person';
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_dashboard_refsecu_indicators AS
SELECT
  SUM(CASE WHEN status = 'awaiting_reference' THEN 1 ELSE 0 END) AS to_reference,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS ready_for_tech_phase,
  SUM(CASE WHEN status = 'cerbere_ok' THEN 1 ELSE 0 END) AS to_invoice,
  SUM(CASE WHEN status = 'awaiting_payment' THEN 1 ELSE 0 END) AS awaiting_payment,
  SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS to_edit_card,
  SUM(CASE WHEN status = 'card_edited' THEN 1 ELSE 0 END) AS to_deliver,
  SUM(CASE WHEN access_decision IS NULL THEN 1 ELSE 0 END) AS undecided
FROM v_person_flattened_step_data
WHERE step_type = 'person';
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_dashboard_sdri_indicators AS
SELECT
  SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS to_review,
  SUM(CASE WHEN status = 'awaiting_info' THEN 1 ELSE 0 END) AS needs_info,
  SUM(CASE WHEN status = 'enquete_prealable' THEN 1 ELSE 0 END) AS under_investigation,
  SUM(CASE WHEN status = 'microcesame' THEN 1 ELSE 0 END) AS tech_in_progress,
  SUM(CASE WHEN status IN ('cerbere_sent','cerbere_ko') THEN 1 ELSE 0 END) AS cerbere_sync,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused_total
FROM v_person_flattened_step_data
WHERE step_type = 'person';
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_dashboard_admin_indicators AS
SELECT
  COUNT(*) AS total_requests,
  COUNT(DISTINCT company_name) AS total_companies,
  SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS drafts,
  SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
  SUM(CASE WHEN status = 'refused' THEN 1 ELSE 0 END) AS refused,
  SUM(CASE WHEN status = 'card_delivered' THEN 1 ELSE 0 END) AS delivered,
  SUM(CASE WHEN status IN ('pending','awaiting_info','awaiting_reference') THEN 1 ELSE 0 END) AS in_process
FROM v_person_flattened_step_data
WHERE step_type = 'person';
SQL);

        // --------------------------------------------------------------------
        // CHARTS (MONTHLY EVOLUTION)
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_chart_requests_monthly_status AS
SELECT
  DATE_FORMAT(STR_TO_DATE(request_date, '%Y-%m-%d'), '%Y-%m') AS month,
  status,
  COUNT(*) AS count
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY month, status
ORDER BY month ASC;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_chart_decision_evolution AS
SELECT
  DATE_FORMAT(STR_TO_DATE(request_date, '%Y-%m-%d'), '%Y-%m') AS month,
  access_decision,
  COUNT(*) AS count
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND access_decision IS NOT NULL
GROUP BY month, access_decision
ORDER BY month ASC;
SQL);

        // --------------------------------------------------------------------
        // RANKINGS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ranking_company_requests AS
SELECT company_name, COUNT(*) AS total_requests
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY company_name
ORDER BY total_requests DESC
LIMIT 20;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ranking_company_refusals AS
SELECT company_name, COUNT(*) AS refusals
FROM v_person_flattened_step_data
WHERE step_type = 'person' AND status = 'refused'
GROUP BY company_name
ORDER BY refusals DESC
LIMIT 20;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ranking_user_activity AS
SELECT employee_email, COUNT(*) AS total_submitted
FROM v_person_flattened_step_data
WHERE step_type = 'person'
GROUP BY employee_email
ORDER BY total_submitted DESC
LIMIT 20;
SQL);

        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_ranking_company_missing_docs AS
SELECT company_name,
       COUNT(*) AS incomplete_requests
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND (gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL)
GROUP BY company_name
ORDER BY incomplete_requests DESC
LIMIT 20;
SQL);

        // --------------------------------------------------------------------
        // ALERTS
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_alerts_user AS
SELECT
  step_id,
  employee_email,
  status,
  request_date,
  'Documents manquants' AS alert_type
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND (gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL)
  AND status IN ('draft','awaiting_info');
SQL);

        // --------------------------------------------------------------------
        // DEADLINES
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_deadlines_user AS
SELECT
  step_id,
  employee_email,
  contract_end_date,
  fluxel_training_date,
  DATEDIFF(STR_TO_DATE(contract_end_date,'%Y-%m-%d'), CURDATE()) AS days_until_contract_end,
  DATEDIFF(DATE_ADD(STR_TO_DATE(fluxel_training_date,'%Y-%m-%d'), INTERVAL 12 MONTH), CURDATE()) AS days_until_training_expire
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND status IN ('approved','card_delivered');
SQL);

        // --------------------------------------------------------------------
        // TO-DO (INTERNAL TASKS)
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_todo_user AS
SELECT
  step_id,
  status,
  employee_email,
  'Compléter les documents manquants' AS todo_type
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND employee_email IS NOT NULL
  AND (status IN ('draft','awaiting_info')
       OR gies_1 IS NULL OR atex_0 IS NULL OR zar IS NULL OR signature IS NULL);
SQL);

        // --------------------------------------------------------------------
        // RECENT / PRIORITARY FILES
        // --------------------------------------------------------------------
        $this->addSql(<<<SQL
CREATE OR REPLACE VIEW v_recent_user_requests AS
SELECT
  step_id,
  employee_email,
  status,
  request_date,
  DATEDIFF(NOW(), STR_TO_DATE(request_date,'%Y-%m-%d')) AS days_since,
  CASE
    WHEN status = 'awaiting_info' THEN '🔴 Besoin d’action'
    WHEN status = 'approved' THEN '🟢 Approuvée'
    WHEN status = 'draft' THEN '⚪ En cours de rédaction'
    ELSE '🟠 Suivi'
  END AS priority
FROM v_person_flattened_step_data
WHERE step_type = 'person'
  AND STR_TO_DATE(request_date,'%Y-%m-%d') >= DATE_SUB(NOW(), INTERVAL 15 DAY);
SQL);

        // --------------------------------------------------------------------
        // RATIOS / COMPARATORS
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
    }

    public function down(Schema $schema): void
    {
        $views = [
            'v_dashboard_user_indicators',
            'v_dashboard_refsecu_indicators',
            'v_dashboard_sdri_indicators',
            'v_dashboard_admin_indicators',
            'v_chart_requests_monthly_status',
            'v_chart_decision_evolution',
            'v_ranking_company_requests',
            'v_ranking_company_refusals',
            'v_ranking_user_activity',
            'v_ranking_company_missing_docs',
            'v_alerts_user',
            'v_deadlines_user',
            'v_todo_user',
            'v_recent_user_requests',
            'v_ratio_company_performance'
        ];
        foreach ($views as $view) {
            $this->addSql("DROP VIEW IF EXISTS `$view`;");
        }
    }
}
