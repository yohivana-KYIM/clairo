<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

final class Version20230921071201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create demande_titre_circulation and detach user_id FKs from several tables (defensive, idempotent).';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        // ---- (1) Create table (idempotent) ----
        $this->addSql("
            CREATE TABLE IF NOT EXISTS demande_titre_circulation (
                id INT AUTO_INCREMENT NOT NULL,
                intervention_id INT DEFAULT NULL,
                etatcivil_id INT DEFAULT NULL,
                filiation_id INT DEFAULT NULL,
                adresse_id INT DEFAULT NULL,
                infocomplementaire_id INT DEFAULT NULL,
                documentpersonnel_id INT DEFAULT NULL,
                documentprofessionnel_id INT DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                ip VARCHAR(255) DEFAULT NULL,
                UNIQUE INDEX UNIQ_C52149D98EAE3863 (intervention_id),
                UNIQUE INDEX UNIQ_C52149D9F7560086 (etatcivil_id),
                UNIQUE INDEX UNIQ_C52149D9DE3E023A (filiation_id),
                UNIQUE INDEX UNIQ_C52149D94DE7DC5C (adresse_id),
                UNIQUE INDEX UNIQ_C52149D9AACB1B2F (infocomplementaire_id),
                UNIQUE INDEX UNIQ_C52149D9C00415E1 (documentpersonnel_id),
                UNIQUE INDEX UNIQ_C52149D9FE5E2AB0 (documentprofessionnel_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ");

        // ---- (2) Add FKs only if missing (avoid duplicate constraint errors) ----
        // ON DELETE SET NULL fits the nullable columns; adjust if you prefer CASCADE/RESTRICT.
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_INTERVENTION',         'intervention_id',         'intervention');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_ETAT_CIVIL',           'etatcivil_id',            'etat_civil');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_FILIATION',            'filiation_id',            'filiation');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_ADRESSE',              'adresse_id',              'adresse');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_INFO_COMPLEMENTAIRE',  'infocomplementaire_id',   'info_complementaire');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_DOC_PERS',             'documentpersonnel_id',    'document_personnel');
        $this->addFkIfMissing('demande_titre_circulation', 'FK_DTC_DOC_PRO',              'documentprofessionnel_id','document_professionnel');

        // ---- (3) Safely drop user_id (FK → index → column) across multiple tables ----
        foreach ([
                     'adresse',
                     'document_personnel',
                     'document_professionnel',
                     'entreprise',
                     'etat_civil',
                     'filiation',
                     'info_complementaire',
                     'intervention',
                 ] as $table) {
            $this->dropFkIndexAndColumnIfExists($table, 'user_id');
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        // Reverse creation
        $this->addSql('DROP TABLE IF EXISTS demande_titre_circulation');

        // Re-add user_id + index + FK with deterministic names
        foreach ([
                     'adresse',
                     'document_personnel',
                     'document_professionnel',
                     'entreprise',
                     'etat_civil',
                     'filiation',
                     'info_complementaire',
                     'intervention',
                 ] as $table) {
            $this->addSql(sprintf('ALTER TABLE %s ADD user_id INT DEFAULT NULL', $table));
            $indexName = $this->safeIndexName($table, 'user_id');
            $this->addSql(sprintf('CREATE INDEX %s ON %s (user_id)', $indexName, $table));
            $fkName    = $this->safeFkName($table, 'user_id');
            $this->addSql(sprintf(
                'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (user_id) REFERENCES user (id)',
                $table,
                $fkName
            ));
        }
    }

    // ---------- Helpers ----------

    /**
     * Drop any FK that references $column on $table, then any index on that column, then the column itself.
     * Handles name drift across environments and correct FK→index→column order.
     */
    private function dropFkIndexAndColumnIfExists(string $table, string $column): void
    {
        /** @var AbstractSchemaManager $sm */
        $sm = $this->connection->createSchemaManager();

        // 1) Drop FK(s) referencing this column (names can differ across envs)
        foreach ($sm->listTableForeignKeys($table) as $fk) {
            $localCols = array_map('strtolower', $fk->getLocalColumns());
            if (in_array(strtolower($column), $localCols, true)) {
                $this->addSql(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $fk->getName()));
            }
        }

        // 2) Drop index(es) on the column if present
        foreach ($sm->listTableIndexes($table) as $index) {
            $cols = array_map('strtolower', $index->getColumns());
            if (in_array(strtolower($column), $cols, true)) {
                $this->addSql(sprintf('DROP INDEX %s ON %s', $index->getName(), $table));
            }
        }

        // 3) Drop the column if it exists
        $columns = $sm->listTableColumns($table);
        if (array_key_exists(strtolower($column), array_change_key_case($columns, CASE_LOWER))) {
            $this->addSql(sprintf('ALTER TABLE %s DROP COLUMN %s', $table, $column));
        }
    }

    /**
     * Add a foreign key only if missing (by name). Defaults: ON DELETE SET NULL, ON UPDATE NO ACTION.
     */
    private function addFkIfMissing(
        string $table,
        string $fkName,
        string $localColumn,
        string $refTable,
        string $refColumn = 'id',
        string $onDelete = 'SET NULL',
        string $onUpdate = 'NO ACTION'
    ): void {
        /** @var AbstractSchemaManager $sm */
        $sm  = $this->connection->createSchemaManager();
        $fks = $sm->listTableForeignKeys($table);

        foreach ($fks as $fk) {
            if (strcasecmp($fk->getName(), $fkName) === 0) {
                return; // FK already present
            }
        }

        $this->addSql(sprintf(
            'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
            $table,
            $fkName,
            $localColumn,
            $refTable,
            $refColumn,
            $onDelete,
            $onUpdate
        ));
    }

    /**
     * Deterministic, MySQL-friendly names for down() artifacts.
     */
    private function safeIndexName(string $table, string $column): string
    {
        return strtoupper(sprintf('IDX_%s_%s', $this->shortHash($table), $this->shortHash($column)));
    }

    private function safeFkName(string $table, string $column): string
    {
        return strtoupper(sprintf('FK_%s_%s', $this->shortHash($table), $this->shortHash($column)));
    }

    private function shortHash(string $s): string
    {
        return substr(hash('sha1', $s), 0, 6);
    }
}
