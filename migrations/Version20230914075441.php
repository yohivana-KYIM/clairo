<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914075441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acte_naissance ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE atex0 ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE attestation_hebergeant ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE autre_document ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE casier_judiciaire ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE document_identite ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE gies0 ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE gies1 ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE gies2 ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE identite_hebergeant ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE justificatif_domicile ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_identite ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE titre_sejour ADD original_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acte_naissance DROP original_name');
        $this->addSql('ALTER TABLE atex0 DROP original_name');
        $this->addSql('ALTER TABLE attestation_hebergeant DROP original_name');
        $this->addSql('ALTER TABLE autre_document DROP original_name');
        $this->addSql('ALTER TABLE casier_judiciaire DROP original_name');
        $this->addSql('ALTER TABLE document_identite DROP original_name');
        $this->addSql('ALTER TABLE gies0 DROP original_name');
        $this->addSql('ALTER TABLE gies1 DROP original_name');
        $this->addSql('ALTER TABLE gies2 DROP original_name');
        $this->addSql('ALTER TABLE identite_hebergeant DROP original_name');
        $this->addSql('ALTER TABLE justificatif_domicile DROP original_name');
        $this->addSql('ALTER TABLE photo_identite DROP original_name');
        $this->addSql('ALTER TABLE titre_sejour DROP original_name');
    }
}
