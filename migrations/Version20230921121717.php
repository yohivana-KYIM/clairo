<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921121717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE document_personnel ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE document_professionnel ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE etat_civil ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE filiation ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE info_complementaire ADD submited TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD submited TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP submited');
        $this->addSql('ALTER TABLE document_personnel DROP submited');
        $this->addSql('ALTER TABLE document_professionnel DROP submited');
        $this->addSql('ALTER TABLE etat_civil DROP submited');
        $this->addSql('ALTER TABLE filiation DROP submited');
        $this->addSql('ALTER TABLE info_complementaire DROP submited');
        $this->addSql('ALTER TABLE intervention DROP submited');
    }
}
