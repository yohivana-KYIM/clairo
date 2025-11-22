<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020134338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add email_auth_code_generated_at column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "ALTER TABLE `user`
         ADD `email_auth_code_generated_at` DATETIME DEFAULT NULL
         COMMENT '(DC2Type:datetime_immutable)'"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP email_auth_code_generated_at');
    }

}
