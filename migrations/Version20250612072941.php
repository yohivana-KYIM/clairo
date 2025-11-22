<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612072941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create flattened view for person step_data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_person_flattened_step_data');

        $this->addSql("
            CREATE VIEW v_person_flattened_step_data AS
            SELECT
                sd.step_id,
                sd.step_number,
                sd.cesar_step_id,
                sd.cesar_step_line,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.request_date')) AS request_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.company_name')) AS company_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.address')) AS address,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.postal_code')) AS postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.city')) AS city,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.country')) AS country,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siren')) AS siren,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.naf')) AS naf,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siret')) AS siret,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.vat_number')) AS vat_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_duration')) AS access_duration_step1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_type')) AS access_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_purpose')) AS access_purpose,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_name')) AS security_officer_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_position')) AS security_officer_position,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_phone')) AS security_officer_phone,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.gender')) AS gender,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.maiden_name')) AS maiden_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birthdate')) AS birthdate,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birthplace')) AS birthplace,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birth_postale_code')) AS birth_postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birth_district')) AS birth_district,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.nationality')) AS nationality,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.social_security_number')) AS social_security_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_email')) AS employee_email,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_phone')) AS employee_phone,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.section_employee_address')) AS employee_address,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.postal_code')) AS employee_postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.city')) AS employee_city,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.country')) AS employee_country,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.resident_situation')) AS resident_situation,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.father_name')) AS father_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.father_first_name')) AS father_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.mother_maiden_name')) AS mother_maiden_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.mother_first_name')) AS mother_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.contract_type')) AS contract_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_function')) AS employee_function,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employment_date')) AS employment_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.contract_end_date')) AS contract_end_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.numero_cni')) AS numero_cni,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.fluxel_training')) AS fluxel_training_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.gies_1')) AS gies_1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.gies_2')) AS gies_2,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.atex_0')) AS atex_0,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.zar')) AS zar,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.health')) AS health,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.id_card')) AS id_card,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.passport')) AS passport,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.photo')) AS photo,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.proof_of_address_host')) AS proof_of_address_host,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.zar_decision')) AS zar_decision,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_atex_0')) AS doc_atex_0,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_gies_1')) AS doc_gies_1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_gies_2')) AS doc_gies_2,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.health_attestation')) AS health_attestation,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.taxi_card')) AS taxi_card,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.general_conditions')) AS general_conditions,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.accept_terms')) AS accept_terms,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.card_place')) AS card_place,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.observations')) AS observations,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.access_duration')) AS access_duration_step6,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.signature')) AS signature,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.access_decision')) AS access_decision,

                sd.status,
                sd.step_type,
                sd.persistance_type

            FROM step_data sd
            WHERE sd.step_type = 'person'
        ");

        $this->addSql('DROP VIEW IF EXISTS v_person_flattened_step_data');

        $this->addSql("
            CREATE VIEW v_person_flattened_step_data AS
            SELECT
                sd.step_id,
                sd.step_number,
                sd.cesar_step_id,
                sd.cesar_step_line,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.request_date')) AS request_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.company_name')) AS company_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.address')) AS address,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.postal_code')) AS postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.city')) AS city,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.country')) AS country,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siren')) AS siren,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.naf')) AS naf,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siret')) AS siret,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.vat_number')) AS vat_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_duration')) AS access_duration_step1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_type')) AS access_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.access_purpose')) AS access_purpose,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_name')) AS security_officer_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_position')) AS security_officer_position,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_email')) AS security_officer_email,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_phone')) AS security_officer_phone,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.gender')) AS gender,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_first_name')) AS employee_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_last_name')) AS employee_last_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.maiden_name')) AS maiden_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birthdate')) AS birthdate,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birthplace')) AS birthplace,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birth_postale_code')) AS birth_postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_birth_district')) AS birth_district,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.nationality')) AS nationality,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.social_security_number')) AS social_security_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_email')) AS employee_email,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_phone')) AS employee_phone,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.section_employee_address')) AS employee_address,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.postal_code')) AS employee_postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.city')) AS employee_city,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.country')) AS employee_country,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.resident_situation')) AS resident_situation,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.father_name')) AS father_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.father_first_name')) AS father_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.mother_maiden_name')) AS mother_maiden_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.mother_first_name')) AS mother_first_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.contract_type')) AS contract_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employee_function')) AS employee_function,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.employment_date')) AS employment_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.contract_end_date')) AS contract_end_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_two.numero_cni')) AS numero_cni,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.fluxel_training')) AS fluxel_training_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.gies_1')) AS gies_1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.gies_2')) AS gies_2,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.atex_0')) AS atex_0,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.zar')) AS zar,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_three.health')) AS health,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.id_card')) AS id_card,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.passport')) AS passport,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.photo')) AS photo,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.proof_of_address_host')) AS proof_of_address_host,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.zar_decision')) AS zar_decision,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_atex_0')) AS doc_atex_0,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_gies_1')) AS doc_gies_1,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.doc_gies_2')) AS doc_gies_2,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.health_attestation')) AS health_attestation,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_five.taxi_card')) AS taxi_card,

                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.general_conditions')) AS general_conditions,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.accept_terms')) AS accept_terms,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.card_place')) AS card_place,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.observations')) AS observations,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.access_duration')) AS access_duration_step6,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.signature')) AS signature,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_six.access_decision')) AS access_decision,

                sd.status,
                sd.step_type,
                sd.persistance_type

            FROM step_data sd
            WHERE sd.step_type = 'person'
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_person_flattened_step_data');
    }
}
