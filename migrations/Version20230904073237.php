<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904073237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etat_civil (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, prenom2 VARCHAR(255) DEFAULT NULL, prenom3 VARCHAR(255) DEFAULT NULL, prenom4 VARCHAR(255) DEFAULT NULL, date_naissance DATE DEFAULT NULL, pays_naissance VARCHAR(255) DEFAULT NULL, lieu_naissance VARCHAR(255) DEFAULT NULL, cp_naissance VARCHAR(255) DEFAULT NULL, arrondissement_naissance VARCHAR(255) DEFAULT NULL, nom_marital VARCHAR(255) DEFAULT NULL, nationalite VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE etat_civil');
    }
}
