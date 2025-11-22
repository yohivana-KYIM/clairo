<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250703140127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_referent_verified column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD is_referent_verified TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql("UPDATE user set is_referent_verified = 1 where email like '%fluxel%' or roles LIKE '%REFSEC%' or roles LIKE '%SDRI%' or roles LIKE '%ADMIN%' ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP is_referent_verified');
    }
}
