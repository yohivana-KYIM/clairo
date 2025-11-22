<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013171344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes manquantes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE entreprise
                ADD COLUMN email_entreprise VARCHAR(255) NULL AFTER num_telephone,
                ADD COLUMN fonction_referent VARCHAR(255) NULL AFTER nom_responsable,
                ADD COLUMN nom_suppleant1 VARCHAR(255) NULL AFTER suppleant1,
                ADD COLUMN nom_suppleant2 VARCHAR(255) NULL AFTER suppleant2
        ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE entreprise
                DROP COLUMN email_entreprise,
                DROP COLUMN fonction_referent,
                DROP COLUMN nom_suppleant1,
                DROP COLUMN nom_suppleant2
        ");

    }
}
