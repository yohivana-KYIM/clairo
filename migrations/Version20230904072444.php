<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904072444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D11814ABA76ED395 ON intervention (user_id)');
        $this->addSql('ALTER TABLE user DROP titre, DROP nom, DROP prenom, DROP prenom2, DROP prenom3, DROP prenom4, DROP date_naissance, DROP pays_naissance, DROP lieu_naissance, DROP cp_naissance, DROP arrondissement_naissance, DROP nom_marital, DROP nationalite, DROP referent_securite');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABA76ED395');
        $this->addSql('DROP INDEX IDX_D11814ABA76ED395 ON intervention');
        $this->addSql('ALTER TABLE intervention DROP user_id');
        $this->addSql('ALTER TABLE user ADD titre VARCHAR(10) DEFAULT NULL, ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD prenom2 VARCHAR(255) DEFAULT NULL, ADD prenom3 VARCHAR(255) DEFAULT NULL, ADD prenom4 VARCHAR(255) DEFAULT NULL, ADD date_naissance DATE DEFAULT NULL, ADD pays_naissance VARCHAR(255) DEFAULT NULL, ADD lieu_naissance VARCHAR(255) DEFAULT NULL, ADD cp_naissance VARCHAR(255) DEFAULT NULL, ADD arrondissement_naissance VARCHAR(255) DEFAULT NULL, ADD nom_marital VARCHAR(255) DEFAULT NULL, ADD nationalite VARCHAR(255) DEFAULT NULL, ADD referent_securite VARCHAR(255) DEFAULT NULL');
    }
}
