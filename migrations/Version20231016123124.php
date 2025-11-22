<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016123124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_titre_circulation ADD entreprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_titre_circulation ADD CONSTRAINT FK_C52149D9A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_C52149D9A4AEAFEA ON demande_titre_circulation (entreprise_id)');
        $this->addSql('ALTER TABLE entreprise ADD adresse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA604DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse_entreprise (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA604DE7DC5C ON entreprise (adresse_id)');
        $this->addSql('ALTER TABLE filiation CHANGE submited submited TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_titre_circulation DROP FOREIGN KEY FK_C52149D9A4AEAFEA');
        $this->addSql('DROP INDEX IDX_C52149D9A4AEAFEA ON demande_titre_circulation');
        $this->addSql('ALTER TABLE demande_titre_circulation DROP entreprise_id');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA604DE7DC5C');
        $this->addSql('DROP INDEX UNIQ_D19FA604DE7DC5C ON entreprise');
        $this->addSql('ALTER TABLE entreprise DROP adresse_id');
        $this->addSql('ALTER TABLE filiation CHANGE submited submited TINYINT(1) DEFAULT NULL');
    }
}
