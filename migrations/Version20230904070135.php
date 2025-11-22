<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904070135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE titre_sejour (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_personnel ADD identity_id INT DEFAULT NULL, ADD photo_id INT DEFAULT NULL, ADD casier_id INT DEFAULT NULL, ADD acte_naiss_id INT DEFAULT NULL, ADD domicile_id INT DEFAULT NULL, ADD hebergement_id INT DEFAULT NULL, ADD ident_hebergent_id INT DEFAULT NULL, ADD sejour_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7FFF3ED4A8 FOREIGN KEY (identity_id) REFERENCES document_identite (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo_identite (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F643911C6 FOREIGN KEY (casier_id) REFERENCES casier_judiciaire (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7FCEA0B946 FOREIGN KEY (acte_naiss_id) REFERENCES acte_naissance (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F95715F7D FOREIGN KEY (domicile_id) REFERENCES justificatif_domicile (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F23BB0F66 FOREIGN KEY (hebergement_id) REFERENCES attestation_hebergeant (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F266B14AC FOREIGN KEY (ident_hebergent_id) REFERENCES identite_hebergeant (id)');
        $this->addSql('ALTER TABLE document_personnel ADD CONSTRAINT FK_B484DB7F84CF0CF FOREIGN KEY (sejour_id) REFERENCES titre_sejour (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7FFF3ED4A8 ON document_personnel (identity_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F7E9E4C8C ON document_personnel (photo_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F643911C6 ON document_personnel (casier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7FCEA0B946 ON document_personnel (acte_naiss_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F95715F7D ON document_personnel (domicile_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F23BB0F66 ON document_personnel (hebergement_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F266B14AC ON document_personnel (ident_hebergent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B484DB7F84CF0CF ON document_personnel (sejour_id)');
        $this->addSql('ALTER TABLE document_professionnel ADD gies0_id INT DEFAULT NULL, ADD gies1_id INT DEFAULT NULL, ADD gies2_id INT DEFAULT NULL, ADD atex0_id INT DEFAULT NULL, ADD autre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document_professionnel ADD CONSTRAINT FK_5D8765842E429F88 FOREIGN KEY (gies0_id) REFERENCES gies0 (id)');
        $this->addSql('ALTER TABLE document_professionnel ADD CONSTRAINT FK_5D87658496FEF8ED FOREIGN KEY (gies1_id) REFERENCES gies1 (id)');
        $this->addSql('ALTER TABLE document_professionnel ADD CONSTRAINT FK_5D876584844B5703 FOREIGN KEY (gies2_id) REFERENCES gies2 (id)');
        $this->addSql('ALTER TABLE document_professionnel ADD CONSTRAINT FK_5D87658499F45A10 FOREIGN KEY (atex0_id) REFERENCES atex0 (id)');
        $this->addSql('ALTER TABLE document_professionnel ADD CONSTRAINT FK_5D876584416A67AB FOREIGN KEY (autre_id) REFERENCES autre_document (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D8765842E429F88 ON document_professionnel (gies0_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D87658496FEF8ED ON document_professionnel (gies1_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D876584844B5703 ON document_professionnel (gies2_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D87658499F45A10 ON document_professionnel (atex0_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D876584416A67AB ON document_professionnel (autre_id)');
        $this->addSql('ALTER TABLE intervention ADD autre VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F84CF0CF');
        $this->addSql('DROP TABLE titre_sejour');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7FFF3ED4A8');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F7E9E4C8C');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F643911C6');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7FCEA0B946');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F95715F7D');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F23BB0F66');
        $this->addSql('ALTER TABLE document_personnel DROP FOREIGN KEY FK_B484DB7F266B14AC');
        $this->addSql('DROP INDEX UNIQ_B484DB7FFF3ED4A8 ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F7E9E4C8C ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F643911C6 ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7FCEA0B946 ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F95715F7D ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F23BB0F66 ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F266B14AC ON document_personnel');
        $this->addSql('DROP INDEX UNIQ_B484DB7F84CF0CF ON document_personnel');
        $this->addSql('ALTER TABLE document_personnel DROP identity_id, DROP photo_id, DROP casier_id, DROP acte_naiss_id, DROP domicile_id, DROP hebergement_id, DROP ident_hebergent_id, DROP sejour_id');
        $this->addSql('ALTER TABLE document_professionnel DROP FOREIGN KEY FK_5D8765842E429F88');
        $this->addSql('ALTER TABLE document_professionnel DROP FOREIGN KEY FK_5D87658496FEF8ED');
        $this->addSql('ALTER TABLE document_professionnel DROP FOREIGN KEY FK_5D876584844B5703');
        $this->addSql('ALTER TABLE document_professionnel DROP FOREIGN KEY FK_5D87658499F45A10');
        $this->addSql('ALTER TABLE document_professionnel DROP FOREIGN KEY FK_5D876584416A67AB');
        $this->addSql('DROP INDEX UNIQ_5D8765842E429F88 ON document_professionnel');
        $this->addSql('DROP INDEX UNIQ_5D87658496FEF8ED ON document_professionnel');
        $this->addSql('DROP INDEX UNIQ_5D876584844B5703 ON document_professionnel');
        $this->addSql('DROP INDEX UNIQ_5D87658499F45A10 ON document_professionnel');
        $this->addSql('DROP INDEX UNIQ_5D876584416A67AB ON document_professionnel');
        $this->addSql('ALTER TABLE document_professionnel DROP gies0_id, DROP gies1_id, DROP gies2_id, DROP atex0_id, DROP autre_id');
        $this->addSql('ALTER TABLE intervention DROP autre');
    }
}
