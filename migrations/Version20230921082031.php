<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921082031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_titre_circulation ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande_titre_circulation ADD CONSTRAINT FK_C52149D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C52149D9A76ED395 ON demande_titre_circulation (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_titre_circulation DROP FOREIGN KEY FK_C52149D9A76ED395');
        $this->addSql('DROP INDEX IDX_C52149D9A76ED395 ON demande_titre_circulation');
        $this->addSql('ALTER TABLE demande_titre_circulation DROP user_id');
    }
}
