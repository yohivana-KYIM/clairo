<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603151622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add the column if not exists
        $this->addSql("ALTER TABLE app_settings ADD options JSON DEFAULT NULL");

        // Optional: update existing settings with options
        $this->addSql("
        UPDATE app_settings SET options = '[\"daily\", \"weekly\"]' WHERE name = 'notification_frequency';
        UPDATE app_settings SET options = '[\"10\", \"20\", \"50\", \"100\"]' WHERE name = 'items_per_page';
        UPDATE app_settings SET options = '[\"date_asc\", \"date_desc\", \"status\"]' WHERE name = 'default_sorting';
        UPDATE app_settings SET options = '[\"light\", \"dark\"]' WHERE name = 'color_theme';
        UPDATE app_settings SET options = '[\"normal\", \"large\", \"extra-large\"]' WHERE name = 'interface_font_size';
        UPDATE app_settings SET options = '[\"table\", \"card\"]' WHERE name = 'table_layout';
        UPDATE app_settings SET options = '[\"low\", \"medium\", \"high\"]' WHERE name = 'password_policy';
        UPDATE app_settings SET options = '[\"never\", \"30\", \"60\", \"90\"]' WHERE name = 'password_expiry';
        UPDATE app_settings SET options = '[\"disabled\", \"email\", \"sms\"]' WHERE name = 'two_factor_auth';
        UPDATE app_settings SET options = '[\"3\", \"5\", \"10\"]' WHERE name = 'login_attempts';
        UPDATE app_settings SET options = '[\"5\", \"15\", \"30\", \"60\"]' WHERE name = 'auto_logout';
        UPDATE app_settings SET options = '[\"sha256\", \"bcrypt\", \"argon2\"]' WHERE name = 'data_encryption';
    ");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
