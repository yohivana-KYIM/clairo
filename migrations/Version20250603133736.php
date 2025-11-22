<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250603000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create settings table and seed initial application settings';
    }

    public function up(Schema $schema): void
    {
        // Table creation
        $this->addSql("
            CREATE TABLE IF NOT EXISTS app_settings (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(100) NOT NULL UNIQUE,
                value TEXT DEFAULT NULL,
                type VARCHAR(50) NOT NULL,
                group_name VARCHAR(50) NOT NULL,
                label VARCHAR(150) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");

        // Seed data
        $this->addSql("
            INSERT INTO app_settings (name, value, type, group_name, label) VALUES
            -- Paramètres Généraux
            ('default_request_type', 'Demande standard', 'string', 'Paramètres Généraux', 'settings.default_request_type'),
            ('access_duration', '6', 'int', 'Paramètres Généraux', 'settings.access_duration'),
            ('max_processing_time', '10', 'int', 'Paramètres Généraux', 'settings.max_processing_time'),
            ('system_email', 'noreply@fluxel.fr', 'email', 'Paramètres Généraux', 'settings.system_email'),
            ('notification_frequency', 'daily', 'select', 'Paramètres Généraux', 'settings.notification_frequency'),

            -- Personnalisation
            ('enable_system_notifications', '1', 'bool', 'Personnalisation de l''Interface', 'settings.enable_system_notifications'),
            ('items_per_page', '20', 'select', 'Personnalisation de l''Interface', 'settings.items_per_page'),
            ('default_sorting', 'date_desc', 'select', 'Personnalisation de l''Interface', 'settings.default_sorting'),
            ('color_theme', 'light', 'radio', 'Personnalisation de l''Interface', 'settings.color_theme'),
            ('interface_font_size', 'normal', 'select', 'Personnalisation de l''Interface', 'settings.interface_font_size'),
            ('table_layout', 'table', 'select', 'Personnalisation de l''Interface', 'settings.table_layout'),
            ('email_notifications', '1', 'bool', 'Personnalisation de l''Interface', 'settings.email_notifications'),
            ('push_notifications', '0', 'bool', 'Personnalisation de l''Interface', 'settings.push_notifications'),
            ('deadline_reminders', '1', 'bool', 'Personnalisation de l''Interface', 'settings.deadline_reminders'),

            -- Paramètres de Sécurité
            ('password_policy', 'medium', 'select', 'Paramètres de Sécurité', 'settings.password_policy'),
            ('password_expiry', '60', 'select', 'Paramètres de Sécurité', 'settings.password_expiry'),
            ('force_password_change', '0', 'bool', 'Paramètres de Sécurité', 'settings.force_password_change'),
            ('two_factor_auth', 'disabled', 'radio', 'Paramètres de Sécurité', 'settings.two_factor_auth'),
            ('login_attempts', '5', 'select', 'Paramètres de Sécurité', 'settings.login_attempts'),
            ('auto_logout', '15', 'select', 'Paramètres de Sécurité', 'settings.auto_logout'),
            ('data_encryption', 'bcrypt', 'select', 'Paramètres de Sécurité', 'settings.data_encryption'),
            ('log_user_activity', '1', 'bool', 'Paramètres de Sécurité', 'settings.log_user_activity'),
            ('delete_old_logs', '0', 'bool', 'Paramètres de Sécurité', 'settings.delete_old_logs');
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE IF EXISTS app_settings");
    }
}
