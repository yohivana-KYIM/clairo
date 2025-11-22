<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908074928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add user enterprise view';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE OR REPLACE VIEW v_entreprise AS
                SELECT
                    ROW_NUMBER() OVER (ORDER BY hash_key) AS id,
                    t.*
                FROM (
                    -- Legacy entreprises avec JOIN adresse
                    SELECT DISTINCT
                        -- Hash unique
                        MD5(CONCAT_WS('|',
                            COALESCE(le.siren, ''),
                            COALESCE(le.siret, ''),
                            COALESCE(le.nom, '')
                        )) AS hash_key,
                
                        le.nom                              AS nom,
                        le.code_ape                         AS code_ape,
                        le.signe                            AS signe,
                        le.complement_nom                   AS complement_nom,
                        le.tva_intra_communautaire          AS tva_intra_communautaire,
                        le.secteur                          AS secteur,
                        le.statut                           AS statut,
                        le.type                             AS type,
                        le.nature                           AS nature,
                        le.siret                            AS siret,
                        le.num_telephone                    AS num_telephone,
                        le.nom_responsable                  AS nom_responsable,
                        le.siren                            AS siren,
                        le.naf                              AS naf,
                        le.nationalite                      AS nationalite,
                        le.email_referent                   AS email_referent,
                        le.adresse_id                       AS adresse_id,
                        le.created_at                       AS created_at,
                        le.suppleant1                       AS suppleant1,
                        le.suppleant2                       AS suppleant2,
                        le.gratuit                          AS gratuit,
                        le.entreprise_mere_id               AS entreprise_mere_id,
                        le.telephone_referent               AS telephone_referent,
                        le.telephone_suppleant1             AS telephone_suppleant1,
                        le.telephone_suppleant2             AS telephone_suppleant2,
                
                        -- Adresse complète jointe
                        CONCAT_WS(' ',
                            a.num_voie,
                            a.distribution,
                            a.ville,
                            a.cp,
                            a.pays
                        ) AS address,
                        a.cp      AS postal_code,
                        a.ville   AS city,
                        a.pays    AS country,
                
                        'entreprise' AS source_table
                
                    FROM entreprise le
                    LEFT JOIN adresse a ON le.adresse_id = a.id
                
                    UNION ALL
                
                    -- Step_data avec adresse JSON
                    SELECT DISTINCT
                        MD5(CONCAT_WS('|',
                            JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siren')),
                            JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siret')),
                            JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.company_name'))
                        )) AS hash_key,
                
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.company_name'))           AS nom,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.naf'))                    AS code_ape,
                        NULL                                                                           AS signe,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.enterpise_autocomplete')) AS complement_nom,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.vat_number'))             AS tva_intra_communautaire,
                        NULL                                                                           AS secteur,
                        NULL                                                                           AS statut,
                        NULL                                                                           AS type,
                        NULL                                                                           AS nature,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siret'))                  AS siret,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_phone')) AS num_telephone,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_name'))  AS nom_responsable,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.siren'))                  AS siren,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.naf'))                    AS naf,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.country'))                AS nationalite,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_email')) AS email_referent,
                        NULL                                                                           AS adresse_id,
                        NULL                                                                           AS created_at,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.alternate_referent_name'))  AS suppleant1,
                        NULL                                                                           AS suppleant2,
                        NULL                                                                           AS gratuit,
                        NULL                                                                           AS entreprise_mere_id,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.security_officer_phone')) AS telephone_referent,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.alternate_referent_phone')) AS telephone_suppleant1,
                        NULL                                                                           AS telephone_suppleant2,
                
                        -- Adresse extraite du JSON
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.address'))     AS address,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.postal_code')) AS postal_code,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.city'))        AS city,
                        JSON_UNQUOTE(JSON_EXTRACT(sd.data, '$.person_step_one.country'))     AS country,
                
                        'step_data' AS source_table
                
                    FROM step_data sd
                    WHERE JSON_EXTRACT(sd.data, '$.person_step_one.company_name') IS NOT NULL
                     AND sd.status IN ('microcesame','microcesame_ko','enquete_prealable','investigation_ko', 'tc_temp_ok','cerbere_sent','cerbere_ko','cerbere_ok', 'awaiting_payment','paid','card_edited','card_delivered','payment_doc_ko')
                ) t;

            SQL
        );

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP VIEW IF EXISTS v_gardien_person_steps');
    }
}
