<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;

final class Version20250213084628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make migration idempotent and safe to run multiple times.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        // ---------- Create tables if missing ----------
        $this->createTableIfMissing(
            'info_complementaire_vehicule',
            "CREATE TABLE info_complementaire_vehicule (
                id INT AUTO_INCREMENT NOT NULL,
                num_telephone VARCHAR(255) DEFAULT NULL,
                email VARCHAR(255) DEFAULT NULL,
                submited TINYINT(1) DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        // step_data requires user(id) FK; create only if both make sense
        if ($this->hasTable('user')) {
            $this->createTableIfMissing(
                'step_data',
                "CREATE TABLE step_data (
                    step_id INT AUTO_INCREMENT NOT NULL,
                    user_id INT NOT NULL,
                    step_number VARCHAR(255) NOT NULL,
                    step_type VARCHAR(255) NOT NULL,
                    data JSON NOT NULL COMMENT '(DC2Type:json)',
                    PRIMARY KEY(step_id),
                    CONSTRAINT fk_step_user FOREIGN KEY (user_id) REFERENCES user(id)
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
            );
        }

        // ---------- Drop legacy tables if present ----------
        $this->dropTableIfExists('person_data');
        $this->dropTableIfExists('index_creation_log');

        // ---------- Drop indexes if they exist (by introspection, not by name only) ----------
        $this->dropIndexesIfExist('atex0', [
            'idx_atex0_name',
            'idx_atex0_original_name',
        ]);
        $this->dropIndexesIfExist('autre_document', [
            'idx_autre_document_name',
        ]);
        $this->dropIndexesIfExist('demande_titre_circulation', [
            'idx_demande_titre_circulation_validated_at',
            'idx_demande_titre_circulation_created_at',
            'idx_demande_titre_circulation_ip',
        ]);

        // ---------- FK on demande_titre_vehicule.infocomplementaire_id -> info_complementaire_vehicule(id) ----------
        $vehiculeTable = 'demande_titre_vehicule';
        if ($this->hasTable($vehiculeTable) && $this->hasTable('info_complementaire_vehicule')) {
            // Drop any FK on local column 'infocomplementaire_id'
            $this->dropForeignKeysByLocalColumn($vehiculeTable, 'infocomplementaire_id');

            // Add FK if missing and the column exists
            if ($this->columnExists($vehiculeTable, 'infocomplementaire_id')) {
                $this->addFkIfMissing(
                    $vehiculeTable,
                    'FK_37E591ACAACB1B2F',       // stable name (or change to your naming convention)
                    'infocomplementaire_id',
                    'info_complementaire_vehicule',
                    'id'
                );
            }
        }

        // ---------- Add user.mfa_strategies JSON column if missing (safe default) ----------
        if ($this->hasTable('user') && !$this->columnExists('user', 'mfa_strategies')) {
            // MySQL JSON default: use functional default to avoid quoting issues
            $this->addSql("ALTER TABLE `user` ADD `mfa_strategies` JSON DEFAULT (JSON_ARRAY('email')) COMMENT '(DC2Type:json)'");
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        // Down is also defensive; remove what we added only if present.

        // user.mfa_strategies
        if ($this->hasTable('user') && $this->columnExists('user', 'mfa_strategies')) {
            $this->addSql('ALTER TABLE `user` DROP COLUMN `mfa_strategies`');
        }

        // FK on demande_titre_vehicule.infocomplementaire_id
        if ($this->hasTable('demande_titre_vehicule')) {
            $this->dropForeignKeysByLocalColumn('demande_titre_vehicule', 'infocomplementaire_id');
        }

        // step_data (drop if exists)
        $this->dropTableIfExists('step_data');

        // info_complementaire_vehicule (drop if exists)
        $this->dropTableIfExists('info_complementaire_vehicule');

        // indexes we tried to drop in up(): nothing to undo (dropping is idempotent); no action.
        // person_data / index_creation_log: also nothing to undo if they were dropped.
    }

    // =========================
    //        HELPERS
    // =========================

    private function sm(): AbstractSchemaManager
    {
        /** @var AbstractSchemaManager $sm */
        $sm = $this->connection->createSchemaManager();
        return $sm;
    }

    private function hasTable(string $table): bool
    {
        return $this->sm()->tablesExist([$table]);
    }

    private function columnExists(string $table, string $column): bool
    {
        if (!$this->hasTable($table)) {
            return false;
        }
        $cols = $this->sm()->listTableColumns($table);
        return array_key_exists(strtolower($column), array_change_key_case($cols, CASE_LOWER));
    }

    /** Create table only if missing */
    private function createTableIfMissing(string $table, string $createSql): void
    {
        if (!$this->hasTable($table)) {
            $this->addSql($createSql);
        }
    }

    /** Drop table only if present */
    private function dropTableIfExists(string $table): void
    {
        if ($this->hasTable($table)) {
            $this->addSql(sprintf('DROP TABLE %s', $table));
        }
    }

    /** Drop by provided names if they exist on the table (case-insensitive). */
    private function dropIndexesIfExist(string $table, array $indexNames): void
    {
        if (!$this->hasTable($table)) {
            return;
        }
        /** @var array<string,Index> $existing */
        $existing = $this->sm()->listTableIndexes($table);
        // Normalize keys for case-insensitive match
        $normalized = [];
        foreach ($existing as $name => $idx) {
            $normalized[strtolower($name)] = $idx;
        }
        foreach ($indexNames as $name) {
            $key = strtolower($name);
            if (isset($normalized[$key])) {
                $this->addSql(sprintf('DROP INDEX %s ON %s', $normalized[$key]->getName(), $table));
            }
        }
    }

    /** Drop all FKs that reference a given local column (handles name drift). */
    private function dropForeignKeysByLocalColumn(string $table, string $column): void
    {
        if (!$this->hasTable($table) || !$this->columnExists($table, $column)) {
            return;
        }
        /** @var ForeignKeyConstraint[] $fks */
        $fks = $this->sm()->listTableForeignKeys($table);
        foreach ($fks as $fk) {
            $localCols = array_map('strtolower', $fk->getLocalColumns());
            if (in_array(strtolower($column), $localCols, true)) {
                $this->addSql(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $fk->getName()));
            }
        }
    }

    /** Add FK only if missing (by name); ensures local column is indexed (MySQL requirement). */
    private function addFkIfMissing(
        string $table,
        string $fkName,
        string $localColumn,
        string $refTable,
        string $refColumn = 'id',
        string $onDelete = 'SET NULL',
        string $onUpdate = 'NO ACTION'
    ): void {
        if (
            !$this->hasTable($table)
            || !$this->columnExists($table, $localColumn)
            || !$this->hasTable($refTable)
        ) {
            return;
        }

        // Ensure a (non-unique) index exists on the local column
        $this->ensureSingleColumnIndex($table, $localColumn, false);

        foreach ($this->sm()->listTableForeignKeys($table) as $fk) {
            if (strcasecmp($fk->getName(), $fkName) === 0) {
                return; // already present
            }
        }

        $this->addSql(sprintf(
            'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s ON UPDATE %s',
            $table, $fkName, $localColumn, $refTable, $refColumn, $onDelete, $onUpdate
        ));
    }

    /** Ensure single-column index exists with desired uniqueness; replace if a single-col index exists with different uniqueness. */
    private function ensureSingleColumnIndex(string $table, string $column, bool $unique): void
    {
        if (!$this->hasTable($table) || !$this->columnExists($table, $column)) {
            return;
        }

        $existing = $this->sm()->listTableIndexes($table);
        foreach ($existing as $idx) {
            $cols = array_map('strtolower', $idx->getColumns());
            if (count($cols) === 1 && $cols[0] === strtolower($column)) {
                if ($idx->isUnique() === $unique) {
                    return; // already satisfied
                }
                // Replace with desired uniqueness
                $this->addSql(sprintf('DROP INDEX %s ON %s', $idx->getName(), $table));
                break;
            }
        }

        $name = $unique
            ? sprintf('UNIQ_%s_%s', strtoupper($table), strtoupper($column))
            : sprintf('IDX_%s_%s',  strtoupper($table), strtoupper($column));

        $this->addSql(sprintf(
            'CREATE %s INDEX %s ON %s (%s)',
            $unique ? 'UNIQUE' : '',
            $name,
            $table,
            $column
        ));
    }
}
