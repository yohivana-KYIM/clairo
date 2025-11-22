<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020014555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO app_settings (name, value, type, group_name, label) VALUES
('sdri_receive_refsec_email', '1', 'bool', 'Paramètres de Sécurité', 'settings.sdri_receive_refsec_email');
            ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM app_settings
            WHERE name = 'sdri_receive_refsec_email'
              AND type = 'bool'
              AND group_name = 'Paramètres de Sécurité'
              AND label = 'settings.sdri_receive_refsec_email'
            LIMIT 1;
        ");
    }
}
