<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use OTPHP\TOTP;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226144820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Generate TOTP secrets for existing users
        $this->generateTotpSecrets();
    }

    public function down(Schema $schema): void
    {
    }

    private function generateTotpSecrets()
    {
        $connection = $this->connection;
        $users = $connection->fetchAllAssociative('SELECT id FROM user WHERE google_authenticator_secret IS NULL');

        foreach ($users as $user) {
            $totp = TOTP::generate();
            $secret = $totp->getSecret();
            $connection->executeStatement('UPDATE user SET google_authenticator_secret = :secret WHERE id = :id', [
                'secret' => $secret,
                'id' => $user['id'],
            ]);
        }
    }
}
