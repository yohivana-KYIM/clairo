<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611133456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE step_data ADD cesar_step_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE step_data ADD cesar_step_line VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE step_data DROP cesar_step_id');
        $this->addSql('ALTER TABLE step_data DROP cesar_step_line');
    }
}
