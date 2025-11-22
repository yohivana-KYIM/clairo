<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250619102313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insérer la configuration refsec_team_emails et refsec_team_cc_emails';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO app_settings (name, value, type, group_name, label, options)
            VALUES (
                'refsec_team_emails',
                'cyril.ortega@fluxel.fr',
                'string',
                'Paramètres Généraux',
                'settings.refsec_team_emails',
                NULL
            )
        ");

        $this->addSql("
            INSERT INTO app_settings (name, value, type, group_name, label, options)
            VALUES (
                'refsec_team_cc_emails',
                'thierry-ange.parra@orange.fr',
                'string',
                'Paramètres Généraux',
                'settings.refsec_team_cc_emails',
                NULL
            )
        ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM app_settings
            WHERE name = 'refsec_team_emails'
        ");
        $this->addSql("
            DELETE FROM app_settings
            WHERE name = 'refsec_team_cc_emails'
        ");

    }
}
