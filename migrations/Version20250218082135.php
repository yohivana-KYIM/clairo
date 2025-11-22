<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250218082135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MFA columns and JSON backup_codes without invalid DEFAULTs';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Only safe on MySQL/MariaDB.');

        $sm = $this->connection->createSchemaManager();
        $cols = [];
        foreach ($sm->listTableColumns('user') as $c) {
            $cols[strtolower($c->getName())] = true;
        }

        // 1) Colonnes simples (ajouts idempotents)
        if (!isset($cols['auth_code'])) {
            $this->addSql("ALTER TABLE `user` ADD `auth_code` VARCHAR(255) DEFAULT NULL");
        }
        if (!isset($cols['trusted_version'])) {
            $this->addSql("ALTER TABLE `user` ADD `trusted_version` INT UNSIGNED NOT NULL DEFAULT 0");
        }
        if (!isset($cols['totp_secret'])) {
            $this->addSql("ALTER TABLE `user` ADD `totp_secret` VARCHAR(255) DEFAULT NULL");
        }
        if (!isset($cols['google_authenticator_secret'])) {
            $this->addSql("ALTER TABLE `user` ADD `google_authenticator_secret` VARCHAR(255) DEFAULT NULL");
        }

        // 2) backup_codes (JSON) : SANS DEFAULT littéral
        if (!isset($cols['backup_codes'])) {
            // D’abord nullable
            $this->addSql("ALTER TABLE `user` ADD `backup_codes` JSON NULL COMMENT '(DC2Type:json)'");
        }

        // Remplir les NULL existants par []
        $this->addSql("UPDATE `user` SET `backup_codes` = JSON_ARRAY() WHERE `backup_codes` IS NULL");

        // Passer NOT NULL, sans DEFAULT
        $this->addSql("ALTER TABLE `user` MODIFY `backup_codes` JSON NOT NULL COMMENT '(DC2Type:json)'");

        // 3) status => DEFAULT NULL (plus tolérant d’utiliser MODIFY)
        $this->addSql("ALTER TABLE `user` MODIFY `status` VARCHAR(255) DEFAULT NULL");

        // 4) mfa_strategies => laisser NULL si tu le souhaites (ajuste le commentaire si besoin)
        if (isset($cols['mfa_strategies'])) {
            $this->addSql("ALTER TABLE `user` MODIFY `mfa_strategies` JSON DEFAULT NULL COMMENT 'The MFA strategies enabled for this user. Possible values: email, totp, google (DC2Type:json)'");
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Only safe on MySQL/MariaDB.');

        $sm = $this->connection->createSchemaManager();
        $cols = [];
        foreach ($sm->listTableColumns('user') as $c) {
            $cols[strtolower($c->getName())] = true;
        }

        // Revert au plus simple, en testant l’existence
        if (isset($cols['auth_code'])) {
            $this->addSql("ALTER TABLE `user` DROP `auth_code`");
        }
        if (isset($cols['trusted_version'])) {
            $this->addSql("ALTER TABLE `user` DROP `trusted_version`");
        }
        if (isset($cols['totp_secret'])) {
            $this->addSql("ALTER TABLE `user` DROP `totp_secret`");
        }
        if (isset($cols['google_authenticator_secret'])) {
            $this->addSql("ALTER TABLE `user` DROP `google_authenticator_secret`");
        }
        if (isset($cols['backup_codes'])) {
            $this->addSql("ALTER TABLE `user` DROP `backup_codes`");
        }

        // status NOT NULL si tu veux revenir à l’état antérieur
        $this->addSql("ALTER TABLE `user` MODIFY `status` VARCHAR(255) NOT NULL");
    }
}
