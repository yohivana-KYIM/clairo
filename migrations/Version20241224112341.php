<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241224112341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a JSON field password_history to the user table with encryption support.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'This migration is only safe on MySQL/MariaDB.');

        $exists = (int) $this->connection->fetchOne("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'user'
              AND COLUMN_NAME = 'password_history'
        ");

        if ($exists === 0) {
            // JSON_ARRAY() évite les quotes ambiguës, ok sur MySQL 8/MariaDB 10.4+
            $this->addSql("ALTER TABLE `user` ADD COLUMN `password_history` JSON NOT NULL DEFAULT (JSON_ARRAY())");
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'This migration is only safe on MySQL/MariaDB.');

        $exists = (int) $this->connection->fetchOne("
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'user'
              AND COLUMN_NAME = 'password_history'
        ");

        if ($exists === 1) {
            $this->addSql("ALTER TABLE `user` DROP COLUMN `password_history`");
        }
    }
}
