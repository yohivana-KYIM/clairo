<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422125854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(sql: <<<SQL
            ALTER TABLE entreprise ADD entreprise_mere_id INT DEFAULT NULL;
            ALTER TABLE entreprise ADD CONSTRAINT FK_ENTREPRISE_ENTREPRISE_MERE FOREIGN KEY (entreprise_mere_id) REFERENCES entreprise (id);
            ALTER TABLE entreprise DROP INDEX UNIQ_D19FA6026E94372;
            ALTER TABLE entreprise DROP INDEX UNIQ_D19FA604DE7DC5C;
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(sql: <<<SQL
            ALTER TABLE entreprise DROP FOREIGN KEY FK_ENTREPRISE_ENTREPRISE_MERE;
            ALTER TABLE entreprise DROP entreprise_mere_id;
SQL);
    }
}
