<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514141333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add persistance_type column to step_data table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE step_data ADD persistance_type VARCHAR(255) NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE step_data DROP persistance_type');
    }
}
