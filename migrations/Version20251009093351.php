<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251009093351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create BEFORE INSERT trigger on `user` to normalize NULLs (no DEFAULT() usage in trigger).';
    }

    public function up(Schema $schema): void
    {
        // Idempotent: remove old/broken trigger if it exists
        $this->addSql('DROP TRIGGER IF EXISTS `user_before_insert_nulls_to_defaults`');

        // New safe trigger
        $this->addSql(<<<'SQL'
CREATE TRIGGER `user_before_insert_nulls_to_defaults`
BEFORE INSERT ON `user`
FOR EACH ROW
SET
  -- Concrete fallbacks only (avoid DEFAULT() in triggers)
  NEW.created_at      = IFNULL(NEW.created_at, CURRENT_TIMESTAMP),
  NEW.trusted_version = IFNULL(NEW.trusted_version, 0),

  -- LONGTEXT fields can’t have non-NULL defaults; force empty JSON arrays
  NEW.roles            = IFNULL(NEW.roles, '[]'),
  NEW.password_history = IFNULL(NEW.password_history, '[]'),
  NEW.mfa_strategies   = IFNULL(NEW.mfa_strategies, '[]'),
  NEW.backup_codes     = IFNULL(NEW.backup_codes, '[]');
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS `user_before_insert_nulls_to_defaults`');
    }
}
