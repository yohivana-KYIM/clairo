<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715133103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create SQL views: v_gardien_person_steps';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE OR REPLACE VIEW v_gardien_person_steps AS
            SELECT
                s.step_id,
                s.step_number,
                s.step_type,
                s.status,
                s.user_id,
                s.cesar_step_id,
            
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.siret')) AS siret_data,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.company_name')) AS company_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
                JSON_UNQUOTE(JSON_EXTRACT(s.data, '$.person_step_one.request_date')) AS request_date
            
            FROM step_data s
            
            WHERE
                s.step_type = 'person'
                AND s.status IN ('card_edited', 'card_delivered');
            SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_gardien_person_steps');
    }
}
