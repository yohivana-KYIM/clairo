<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230830122232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acte_naissance (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, tour_etc VARCHAR(255) DEFAULT NULL, escalier_etc VARCHAR(255) DEFAULT NULL, num_voie VARCHAR(255) DEFAULT NULL, cp VARCHAR(255) DEFAULT NULL, distribution VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adresse_entreprise (id INT AUTO_INCREMENT NOT NULL, num_voie VARCHAR(255) DEFAULT NULL, distribution VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, tour_etc VARCHAR(255) DEFAULT NULL, cp VARCHAR(255) DEFAULT NULL, num_telephone VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE atex0 (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attestation_hebergeant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE autre_document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE casier_judiciaire (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_identite (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_personnel (id INT AUTO_INCREMENT NOT NULL, arrondissement_naissance VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_professionnel (id INT AUTO_INCREMENT NOT NULL, date_gies0_debut DATE DEFAULT NULL, date_gies0_fin DATE DEFAULT NULL, date_atex0_debut DATE DEFAULT NULL, date_atex0_fin DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, code_ape VARCHAR(255) DEFAULT NULL, signe VARCHAR(255) DEFAULT NULL, complement_nom VARCHAR(255) DEFAULT NULL, tva_intra_communautaire VARCHAR(255) DEFAULT NULL, secteur VARCHAR(255) DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, nature VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, num_telephone VARCHAR(255) DEFAULT NULL, nom_responsable VARCHAR(255) DEFAULT NULL, siren VARCHAR(255) DEFAULT NULL, naf VARCHAR(255) DEFAULT NULL, nationalite VARCHAR(255) DEFAULT NULL, email_referent VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filiation (id INT AUTO_INCREMENT NOT NULL, nom_pere VARCHAR(255) DEFAULT NULL, prenom_pere VARCHAR(255) DEFAULT NULL, nom_mere VARCHAR(255) DEFAULT NULL, prenom_mere VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gies0 (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gies1 (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gies2 (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE identite_hebergeant (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info_complementaire (id INT AUTO_INCREMENT NOT NULL, num_telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, bat_administration TINYINT(1) DEFAULT NULL, exploitation_fos TINYINT(1) DEFAULT NULL, exploitation_lavera TINYINT(1) DEFAULT NULL, motif VARCHAR(255) DEFAULT NULL, duree VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE justificatif_domicile (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo_identite (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE probleme_carte (id INT AUTO_INCREMENT NOT NULL, motif VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, date_naissance DATE NOT NULL, suite_donner VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD ip VARCHAR(255) DEFAULT NULL, ADD titre VARCHAR(10) DEFAULT NULL, ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD prenom2 VARCHAR(255) DEFAULT NULL, ADD prenom3 VARCHAR(255) DEFAULT NULL, ADD prenom4 VARCHAR(255) DEFAULT NULL, ADD date_naissance DATE DEFAULT NULL, ADD pays_naissance VARCHAR(255) DEFAULT NULL, ADD lieu_naissance VARCHAR(255) DEFAULT NULL, ADD cp_naissance VARCHAR(255) DEFAULT NULL, ADD arrondissement_naissance VARCHAR(255) DEFAULT NULL, ADD nom_marital VARCHAR(255) DEFAULT NULL, ADD nationalite VARCHAR(255) DEFAULT NULL, ADD referent_securite VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE acte_naissance');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE adresse_entreprise');
        $this->addSql('DROP TABLE atex0');
        $this->addSql('DROP TABLE attestation_hebergeant');
        $this->addSql('DROP TABLE autre_document');
        $this->addSql('DROP TABLE casier_judiciaire');
        $this->addSql('DROP TABLE document_identite');
        $this->addSql('DROP TABLE document_personnel');
        $this->addSql('DROP TABLE document_professionnel');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE filiation');
        $this->addSql('DROP TABLE gies0');
        $this->addSql('DROP TABLE gies1');
        $this->addSql('DROP TABLE gies2');
        $this->addSql('DROP TABLE identite_hebergeant');
        $this->addSql('DROP TABLE info_complementaire');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE justificatif_domicile');
        $this->addSql('DROP TABLE photo_identite');
        $this->addSql('DROP TABLE probleme_carte');
        $this->addSql('ALTER TABLE user DROP ip, DROP titre, DROP nom, DROP prenom, DROP prenom2, DROP prenom3, DROP prenom4, DROP date_naissance, DROP pays_naissance, DROP lieu_naissance, DROP cp_naissance, DROP arrondissement_naissance, DROP nom_marital, DROP nationalite, DROP referent_securite');
    }
}
