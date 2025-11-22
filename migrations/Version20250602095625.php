<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20250602095625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add step_data.field_reviews (JSON) without DEFAULT; backfill {}; set NOT NULL. Idempotent. Doctrine-based introspection, no IF NOT EXISTS.';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();

        // Introspect once (safe, portable)
        $hasFieldReviews = false;
        foreach ($sm->listTableColumns('step_data') as $col) {
            if (strcasecmp($col->getName(), 'field_reviews') === 0) {
                $hasFieldReviews = true;
                break;
            }
        }

        // 1) Create column if missing (nullable, NO DEFAULT)
        if (!$hasFieldReviews) {
            // We target MySQL 8 here; if you ever move to MariaDB <10.2,
            // change JSON to LONGTEXT with the DC2Type comment.
            $this->addSql("
                ALTER TABLE `step_data`
                ADD `field_reviews` JSON NULL COMMENT '(DC2Type:json)'
            ");
        } else {
            // Ensure it's JSON & NULLABLE and no stray default/comment mismatch
            $this->addSql("
                ALTER TABLE `step_data`
                MODIFY `field_reviews` JSON NULL COMMENT '(DC2Type:json)'
            ");
        }

        // 2) Backfill {} where NULL or invalid JSON
        // (All queued after the ADD/MODIFY above, so the column exists by execution time.)
        $this->addSql("
            UPDATE `step_data`
            SET `field_reviews` = JSON_OBJECT()
            WHERE `field_reviews` IS NULL
               OR JSON_VALID(`field_reviews`) = 0
        ");

        // 3) Enforce NOT NULL now that backfill removed NULLs
        $this->addSql("
            ALTER TABLE `step_data`
            MODIFY `field_reviews` JSON NOT NULL COMMENT '(DC2Type:json)'
        ");
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        $hasFieldReviews = false;
        foreach ($sm->listTableColumns('step_data') as $col) {
            if (strcasecmp($col->getName(), 'field_reviews') === 0) {
                $hasFieldReviews = true;
                break;
            }
        }

        if ($hasFieldReviews) {
            $this->addSql("ALTER TABLE `step_data` DROP COLUMN `field_reviews`");
        }
    }
}
