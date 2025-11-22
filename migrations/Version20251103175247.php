<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Dynamically recreate all v_* dashboard views with a synthetic SHA1-based id column,
 * preserving DEFINER and SQL SECURITY attributes.
 * Fully compatible with MySQL 5.7+ and MariaDB 10.1+.
 */
final class Version20251103175247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add synthetic SHA1(id) column to dashboard SQL views, preserving DEFINER and SQL SECURITY.';
    }

    public function up(Schema $schema): void
    {
        $views = [
            'v_alerts_user',
            'v_chart_decision_evolution',
            'v_chart_requests_monthly_status',
            'v_dashboard_admin_indicators',
            'v_dashboard_refsecu_indicators',
            'v_dashboard_sdri_indicators',
            'v_dashboard_user_indicators',
            'v_deadlines_user',
            'v_recent_user_requests',
            'v_todo_user',
        ];

        foreach ($views as $viewName) {
            $this->addSyntheticIdToView($viewName);
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('-- Idempotent down(): nothing to revert for synthetic IDs on views.');
    }

    private function addSyntheticIdToView(string $view): void
    {
        // 1️⃣  Verify existence
        $exists = (int)$this->connection->fetchOne("
        SELECT COUNT(*) FROM information_schema.views
        WHERE table_schema = DATABASE() AND table_name = :v
    ", ['v' => $view]);
        if ($exists === 0) {
            $this->addSql("-- ⚠️  {$view}: view not found, skipping");
            return;
        }

        // 2️⃣  Skip if id already present
        $hasId = (int)$this->connection->fetchOne("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = :v AND column_name = 'id'
    ", ['v' => $view]);
        if ($hasId > 0) {
            $this->addSql("-- ✅  {$view}: already has id column");
            return;
        }

        // 3️⃣  Gather meta
        $meta = $this->connection->fetchAssociative("
        SELECT DEFINER, SECURITY_TYPE FROM information_schema.views
        WHERE table_schema = DATABASE() AND table_name = :v
    ", ['v' => $view]);

        // Quote definer safely  `user`@`host`
        $definer = $meta['DEFINER'] ?? 'CURRENT_USER';
        if (preg_match('/^([^@]+)@(.+)$/', $definer, $m)) {
            $definer = sprintf('`%s`@`%s`', trim($m[1], '`'), trim($m[2], '`'));
        } else {
            $definer = '`' . str_replace('`', '', $definer) . '`@`%`';
        }
        $security = strtoupper($meta['SECURITY_TYPE'] ?? 'DEFINER');

        // 4️⃣  Read current view definition
        $row = $this->connection->fetchAssociative("SHOW CREATE VIEW `{$view}`");
        $create = $row['Create View'] ?? null;
        if (!$create || !preg_match('/AS\s+(SELECT.*)/is', $create, $m)) {
            $this->addSql("-- ⚠️  {$view}: could not parse definition");
            return;
        }
        $selectSql = trim($m[1]);

        // 5️⃣  List columns for hash
        $cols = $this->connection->fetchFirstColumn("
        SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = :v
    ", ['v' => $view]);
        $cols = array_filter($cols, fn($c) => strtolower($c) !== 'id');
        $concat = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $hashExpr = "SHA1(CONCAT_WS('-', {$concat})) AS id";

        // 6️⃣  Build final CREATE OR REPLACE statement directly
        $createView = sprintf(
            "CREATE OR REPLACE DEFINER=%s SQL SECURITY %s VIEW `%s` AS " .
            "SELECT %s, t.* FROM (%s) AS t",
            $definer,
            $security,
            $view,
            $hashExpr,
            $selectSql
        );

        $this->addSql("-- 🔁  Recreating {$view} with SHA1 id (preserving definer/security)");
        // execute directly (no PREPARE/REPLACE hacks)
        $this->addSql($createView);
    }
}
