<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610133027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO app_settings (name, value, type, group_name, label) VALUES
            ('cesar_sequence', '800', 'int', 'Paramètres Généraux', 'settings.cesar_sequence')
        ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM app_settings
            WHERE name = 'cesar_sequence'
        ");

    }
}
