<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

final class Version20230921084106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Defensive/idempotent: rename documentpersonnel_id->docpersonnel_id, drop user_id, fix indexes/FKs without hard-coded names.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        $table       = 'demande_titre_circulation';
        $oldCol      = 'documentpersonnel_id';
        $newCol      = 'docpersonnel_id';
        $userCol     = 'user_id';
        $parentTable = 'document_personnel';

        if (!$this->hasTable($table)) {
            return;
        }

        // (1) Drop FKs that reference old/new/user columns
        $this->dropForeignKeysByLocalColumn($table, $oldCol);
        $this->dropForeignKeysByLocalColumn($table, $newCol);
        $this->dropForeignKeysByLocalColumn($table, $userCol);

        // (2) Drop single-column indexes on those columns (no name assumptions)
        $this->dropSingleColumnIndexes($table, $oldCol);
        $this->dropSingleColumnIndexes($table, $newCol);
        $this->dropSingleColumnIndexes($table, $userCol);

        // (3) Rename column (or create if neither exists)
        $hasOld = $this->columnExists($table, $oldCol);
        $hasNew = $this->columnExists($table, $newCol);

        if ($hasOld && !$hasNew) {
            $this->addSql(sprintf('ALTER TABLE %s CHANGE %s %s INT DEFAULT NULL', $table, $oldCol, $newCol));
        } elseif (!$hasOld && !$hasNew) {
            $this->addSql(sprintf('ALTER TABLE %s ADD %s INT DEFAULT NULL', $table, $newCol));
        }
        // If both exist, we leave them (unlikely; manual cleanup may be needed later).

        // (4) Drop user_id column if present
        $this->dropColumnIfExists($table, $userCol);

        // (5) Recreate desired UNIQUE index + FK on newCol
        $this->createSingleColumnIndexIfNeeded($table, $newCol, $unique = true);
        $this->addFkIfMissing(
            $table,
            $this->stableFkName($table, $newCol), // stable FK name
            $newCol,
            $parentTable,
            'id',
            'SET NULL',
            'NO ACTION'
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        $table       = 'demande_titre_circulation';
        $oldCol      = 'documentpersonnel_id';
        $newCol      = 'docpersonnel_id';
        $userCol     = 'user_id';
        $parentTable = 'document_personnel';

        if (!$this->hasTable($table)) {
            return;
        }

        // Drop FK + indexes on newCol
        $this->dropForeignKeysByLocalColumn($table, $newCol);
        $this->dropSingleColumnIndexes($table, $newCol);

        // Rename back if needed
        $hasOld = $this->columnExists($table, $oldCol);
        $hasNew = $this->columnExists($table, $newCol);

        if ($hasNew && !$hasOld) {
            $this->addSql(sprintf('ALTER TABLE %s CHANGE %s %s INT DEFAULT NULL', $table, $newCol, $oldCol));
        } elseif (!$hasNew && !$hasOld) {
            $this->addSql(sprintf('ALTER TABLE %s ADD %s INT DEFAULT NULL', $table, $oldCol));
        }

        // Recreate UNIQUE index and FK on oldCol
        $this->createSingleColumnIndexIfNeeded($table, $oldCol, $unique = true);
        $this->addFkIfMissing(
            $table,
            $this->stableFkName($table, $oldCol),
            $oldCol,
            $parentTable
        );

        // Recreate user_id column + plain index + FK
        if (!$this->columnExists($table, $userCol)) {
            $this->addSql(sprintf('ALTER TABLE %s ADD %s INT DEFAULT NULL', $table, $userCol));
        }
        $this->createSingleColumnIndexIfNeeded($table, $userCol, $unique = false);
        $this->addFkIfMissing(
            $table,
            $this->stableFkName($table, $userCol),
            $userCol,
            'user'
        );
    }

    // ------------- Helpers (defensive, no hard-coded names) -------------

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
        if (!$this->hasTable($table)) return false;
        $cols = $this->sm()->listTableColumns($table);
        return array_key_exists(strtolower($column), array_change_key_case($cols, CASE_LOWER));
    }

    private function dropForeignKeysByLocalColumn(string $table, string $column): void
    {
        if (!$this->hasTable($table) || !$this->columnExists($table, $column)) return;

        foreach ($this->sm()->listTableForeignKeys($table) as $fk) {
            $localCols = array_map('strtolower', $fk->getLocalColumns());
            if (in_array(strtolower($column), $localCols, true)) {
                $this->addSql(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $fk->getName()));
            }
        }
    }

    /**
     * Drop only single-column indexes that target $column.
     * (We do NOT touch composite indexes.)
     */
    private function dropSingleColumnIndexes(string $table, string $column): void
    {
        if (!$this->hasTable($table) || !$this->columnExists($table, $column)) return;

        foreach ($this->sm()->listTableIndexes($table) as $idx) {
            $cols = array_map('strtolower', $idx->getColumns());
            if (count($cols) === 1 && $cols[0] === strtolower($column)) {
                $this->addSql(sprintf('DROP INDEX %s ON %s', $idx->getName(), $table));
            }
        }
    }

    private function dropColumnIfExists(string $table, string $column): void
    {
        if ($this->hasTable($table) && $this->columnExists($table, $column)) {
            $this->addSql(sprintf('ALTER TABLE %s DROP COLUMN %s', $table, $column));
        }
    }

    /**
     * Create a single-column index iff:
     *  - the column exists,
     *  - there is no single-column index with the same uniqueness already.
     * Replaces existing single-column index if uniqueness differs.
     */
    private function createSingleColumnIndexIfNeeded(string $table, string $column, bool $unique): void
    {
        if (!$this->hasTable($table) || !$this->columnExists($table, $column)) return;

        $existing = $this->sm()->listTableIndexes($table);
        foreach ($existing as $idx) {
            $cols = array_map('strtolower', $idx->getColumns());
            if (count($cols) === 1 && $cols[0] === strtolower($column)) {
                if ($idx->isUnique() === $unique) {
                    return; // already matches
                }
            }
        }

        // Replace any single-column indexes on $column to match desired uniqueness
        $this->dropSingleColumnIndexes($table, $column);

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

    private function stableFkName(string $table, string $column): string
    {
        return strtoupper(sprintf('FK_%s_%s', $this->shortHash($table), $this->shortHash($column)));
    }

    private function addFkIfMissing(
        string $table,
        string $fkName,
        string $localColumn,
        string $refTable,
        string $refColumn = 'id',
        string $onDelete = 'SET NULL',
        string $onUpdate = 'NO ACTION'
    ): void {
        if (!$this->hasTable($table) || !$this->columnExists($table, $localColumn)) return;

        // Ensure local index exists (MySQL requirement for FKs)
        $this->createSingleColumnIndexIfNeeded($table, $localColumn, false);

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

    private function shortHash(string $s): string
    {
        return substr(hash('sha1', $s), 0, 6);
    }
}
