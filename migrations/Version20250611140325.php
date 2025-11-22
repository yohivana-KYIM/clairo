<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\ParameterType;

final class Version20250611140325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insère/MAJ la configuration sneas_data_dir de manière idempotente.';
    }

    public function up(Schema $schema): void
    {
        // Only for MySQL/MariaDB
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        // If the settings table is missing, do nothing (defensive).
        if (!$this->hasTable('app_settings')) {
            return;
        }

        $basePath = $_ENV['APP_BASE_PATH'] ?? '/srv/app';
        $path     = rtrim($basePath, '/').'/private/datas/sneas';

        // Note: we inline NULL for `options` to avoid binding a null param (which breaks InlineParameterFormatter).
        $sql = <<<SQL
INSERT INTO app_settings (name, value, type, group_name, label, options)
VALUES (:name, :value, :type, :group_name, :label, NULL)
ON DUPLICATE KEY UPDATE
  value      = VALUES(value),
  type       = VALUES(type),
  group_name = VALUES(group_name),
  label      = VALUES(label),
  options    = NULL
SQL;

        $params = [
            'name'       => 'sneas_data_dir',
            'value'      => $path,
            'type'       => 'string',
            'group_name' => 'Paramètres Généraux',
            'label'      => 'settings.sneas_data_dir',
        ];

        $types = [
            'name'       => ParameterType::STRING,
            'value'      => ParameterType::STRING,
            'type'       => ParameterType::STRING,
            'group_name' => ParameterType::STRING,
            'label'      => ParameterType::STRING,
        ];

        $this->addSql($sql, $params, $types);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'This migration is MySQL-specific.'
        );

        if (!$this->hasTable('app_settings')) {
            return;
        }

        $this->addSql(
            'DELETE FROM app_settings WHERE name = :name',
            ['name' => 'sneas_data_dir'],
            ['name' => ParameterType::STRING]
        );
    }

    // ---------- Helpers ----------

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
}
