<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917144426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table cart_item liée à produit et entreprise_unifiee';
    }

    public function up(Schema $schema): void
    {
        // Création table cart_item
        $this->addSql('CREATE TABLE cart_item (
            id INT AUTO_INCREMENT NOT NULL,
            produit_id INT NOT NULL,
            entreprise_id INT NOT NULL,
            step_id INT NOT NULL,
            step_type VARCHAR(20) NOT NULL,
            quantity INT NOT NULL,
            nom VARCHAR(255) DEFAULT NULL,
            prenom VARCHAR(255) DEFAULT NULL,
            INDEX IDX_CART_ITEM_PRODUIT (produit_id),
            INDEX IDX_CART_ITEM_ENTREPRISE (entreprise_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Contraintes FK pour MySQL/MariaDB (pas de DEFERRABLE)
        $this->addSql('ALTER TABLE cart_item 
            ADD CONSTRAINT FK_CART_ITEM_PRODUIT FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cart_item');
    }
}
