<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251030081051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add history column to entreprise table if not exists (idempotent)';
    }

    public function up(Schema $schema): void
    {
        // Vérifier la présence de la colonne
        $sm = $this->connection->createSchemaManager();
        $columns = $sm->listTableColumns('entreprise');

        if (!array_key_exists('history', $columns)) {
            $this->addSql('ALTER TABLE entreprise ADD history TEXT DEFAULT NULL');
            $this->write('✅ Column "history" added to "entreprise" table.');
        } else {
            $this->write('ℹ️ Column "history" already exists — skipping.');
        }
    }

    public function down(Schema $schema): void
    {
        // Suppression idempotente
        $sm = $this->connection->createSchemaManager();
        $columns = $sm->listTableColumns('entreprise');

        if (array_key_exists('history', $columns)) {
            $this->addSql('ALTER TABLE entreprise DROP COLUMN history');
            $this->write('🗑️ Column "history" dropped from "entreprise" table.');
        } else {
            $this->write('ℹ️ Column "history" does not exist — skipping drop.');
        }
    }
}
