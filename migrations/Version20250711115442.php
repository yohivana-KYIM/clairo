<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711115442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create SQL views: v_person_user_steps, v_person_admin_steps, v_person_refsecu_steps, v_person_sdri_steps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE OR REPLACE VIEW v_person_user_steps AS
            SELECT
              s.step_id,
              s.step_number,
              s.step_type,
              s.status,
              s.cesar_step_id,
              s.user_id,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) AS siret_data,
              COALESCE(e1.siret, e2.siret) AS siret_entreprise,
              COALESCE(e1.email_referent, e2.email_referent) AS email_referent_entreprise,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.company_name')) AS company_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.request_date')) AS request_date
            FROM
              step_data s
              JOIN user u ON u.id = s.user_id
              LEFT JOIN entreprise e1 ON u.entreprise_id = e1.id
              LEFT JOIN entreprise e2 ON JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) = e2.siret
            WHERE
              s.status IN (
                'draft', 'deposit', 'awaiting_reference', 'pending', 'approved', 'refused', 'bad_firm',
                'awaiting_payment', 'paid', 'payment_doc_ko', 'card_edited', 'card_delivered',
                'microcesame_ko', 'investigation_ko', 'tc_temp_ok', 'cerbere_ok'
              )
              AND s.step_type = 'person';
        ");

        $this->addSql("
            CREATE OR REPLACE VIEW v_person_admin_steps AS
            SELECT
              s.step_id,
              s.step_number,
              s.step_type,
              s.status,
              s.cesar_step_id,
              s.user_id,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) AS siret_data,
              COALESCE(e1.siret, e2.siret) AS siret_entreprise,
              COALESCE(e1.email_referent, e2.email_referent) AS email_referent_entreprise,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.company_name')) AS company_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.request_date')) AS request_date
            FROM
              step_data s
              JOIN user u ON u.id = s.user_id
              LEFT JOIN entreprise e1 ON u.entreprise_id = e1.id
              LEFT JOIN entreprise e2 ON JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) = e2.siret
            WHERE
              s.status IN (
                'draft', 'deposit', 'awaiting_reference', 'pending', 'approved', 'refused', 'bad_firm',
                'awaiting_payment', 'paid', 'payment_doc_ko', 'card_edited', 'card_delivered',
                'microcesame_ko', 'investigation_ko', 'tc_temp_ok', 'cerbere_ok'
              )
              AND s.step_type = 'person';
        ");

        $this->addSql("
            CREATE OR REPLACE VIEW v_person_refsecu_steps AS
            SELECT
              s.step_id,
              s.step_number,
              s.step_type,
              s.status,
              s.cesar_step_id,
              s.user_id,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) AS siret_data,
              COALESCE(e1.siret, e2.siret) AS siret_entreprise,
              COALESCE(e1.email_referent, e2.email_referent) AS email_referent_entreprise,
              refu.id AS refsecu_id,
              refu.email AS refsecu_email,
              refent.siret AS refsecu_siret,
              refent.email_referent AS refsecu_email_referent,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.company_name')) AS company_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
              JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.request_date')) AS request_date
            FROM
              step_data s
              JOIN user u ON u.id = s.user_id
              LEFT JOIN entreprise e1 ON u.entreprise_id = e1.id
              LEFT JOIN entreprise e2 ON JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) = e2.siret
              JOIN user refu ON TRUE
              JOIN entreprise refent ON refu.entreprise_id = refent.id
            WHERE
              s.status IN (
                'deposit', 'awaiting_reference', 'pending', 'approved', 'refused', 'bad_firm',
                'awaiting_payment', 'paid', 'payment_doc_ko', 'card_edited', 'card_delivered', 'cerbere_ok'
              )
              AND s.step_type = 'person';
        ");

        $this->addSql("
            CREATE OR REPLACE VIEW v_person_sdri_steps AS
            SELECT
                s.step_id,
                s.step_number,
                s.step_type,
                s.status,
                s.cesar_step_id,
                s.user_id,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.company_name')) AS company_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siren')) AS siren,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.request_date')) AS request_date
            FROM step_data s
            WHERE s.status IN (
                'awaiting_reference', 'pending', 'awaiting_info', 'provisioned', 'approved',
                'refused', 'microcesame', 'microcesame_ko', 'enquete_prealable', 'investigation_ko',
                'tc_temp_ok', 'cerbere_sent', 'cerbere_ok', 'cerbere_ko', 'awaiting_payment', 'payment_doc_ko'
            )
            AND s.step_type = 'person';
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_person_user_steps');
        $this->addSql('DROP VIEW IF EXISTS v_person_admin_steps');
        $this->addSql('DROP VIEW IF EXISTS v_person_refsecu_steps');
        $this->addSql('DROP VIEW IF EXISTS v_person_sdri_steps');
    }
}
