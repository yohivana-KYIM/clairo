<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617084010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create flattened view for person step_data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_vehicle_flattened_step_data');

        $this->addSql("
            CREATE VIEW v_vehicle_flattened_step_data AS
            SELECT
                sd.step_id,
                sd.step_number,
                sd.cesar_step_id,
                sd.cesar_step_line,

                -- Vehicle Step One
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.owner_or_renter')) AS owner_or_renter,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.company_name')) AS company_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.responsible_name')) AS responsible_name,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.security_officer_email')) AS security_officer_email,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.security_officer_phone')) AS security_officer_phone,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.request_date')) AS request_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.access_type')) AS access_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.address')) AS address,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.postal_code')) AS postal_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.city')) AS city,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.country')) AS country,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.siren_number')) AS siren_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.naf_number')) AS naf_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.ape_code')) AS ape_code,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.siret_number')) AS siret_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.vat_number')) AS vat_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_one.email')) AS email,

                -- Vehicle Step Two
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.registration_number')) AS registration_number,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.brand')) AS brand,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.model')) AS model,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.first_registration_date')) AS first_registration_date,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.vehicle_type')) AS vehicle_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.certification_type')) AS certification_type,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_two.gies_expiry_date')) AS gies_expiry_date,

                -- Vehicle Step Three
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_three.fos_port_access')) AS fos_port_access,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_three.lavera_port_access')) AS lavera_port_access,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_three.fos_access_reason')) AS fos_access_reason,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_three.lavera_access_reason')) AS lavera_access_reason,

                -- Vehicle Step Five
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_five.signature')) AS signature_step5,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_five.card_copy')) AS card_copy,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_five.gies_sticker_copy')) AS gies_sticker_copy,

                -- Vehicle Step Six
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_six.terms_and_conditions')) AS terms_and_conditions,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_six.accept_terms')) AS accept_terms,
                JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.vehicle_step_six.signature')) AS signature_step6,

                -- Additional Fields from step_data
                sd.user_id,
                sd.microcesame_id,
                sd.field_reviews,
                sd.status,
                sd.step_type,
                sd.persistance_type

            FROM step_data sd
            WHERE sd.step_type = 'vehicle'
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_vehicle_flattened_step_data');
    }
}
