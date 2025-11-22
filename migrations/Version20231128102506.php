<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128102506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse_facturation (id INT AUTO_INCREMENT NOT NULL, adresse_facturation_entreprise_id INT DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, code_postal VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D9E5A8D5C616BA9F (adresse_facturation_entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse_facturation ADD CONSTRAINT FK_D9E5A8D5C616BA9F FOREIGN KEY (adresse_facturation_entreprise_id) REFERENCES entreprise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse_facturation DROP FOREIGN KEY FK_D9E5A8D5C616BA9F');
        $this->addSql('DROP TABLE adresse_facturation');
    }
}
