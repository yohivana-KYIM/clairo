<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'db:diagnose', description: 'Analyse des requêtes SQL lentes et métadonnées DB')]
class DbDiagnoseCommand extends Command
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dbName = $this->connection->getDatabase();

        $io->title("Diagnostic SQL sur la base : $dbName");

        // 1. Tables volumineuses
        $io->section("Tables volumineuses (> 50 Mo)");
        $tables = $this->connection->fetchAllAssociative("
            SELECT table_name, table_rows,
                   ROUND(data_length/1024/1024,2) AS data_mb,
                   ROUND(index_length/1024/1024,2) AS index_mb
            FROM information_schema.tables
            WHERE table_schema = :db
            ORDER BY data_length DESC
            LIMIT 10
        ", ['db' => $dbName]);
        $io->table(['Table', 'Rows', 'Data (MB)', 'Index (MB)'], $tables);

        // 2. Colonnes sans index
        $io->section("Colonnes sans index utilisées");
        $columns = $this->connection->fetchAllAssociative("
            SELECT table_name, column_name, data_type
            FROM information_schema.columns
            WHERE table_schema = :db
              AND column_key = ''
              AND extra NOT LIKE '%auto_increment%'
        ", ['db' => $dbName]);
        if ($columns) {
            $io->table(['Table', 'Colonne', 'Type'], $columns);
        } else {
            $io->success("Toutes les colonnes critiques semblent indexées 👍");
        }

        // 3. Index inutilisés (Performance Schema requis)
        $io->section("Index non utilisés (selon Performance Schema)");
        $unused = $this->connection->fetchAllAssociative("
            SELECT object_name AS table_name, index_name, count_star
            FROM performance_schema.table_io_waits_summary_by_index_usage
            WHERE object_schema = :db
              AND index_name IS NOT NULL
              AND count_star = 0
        ", ['db' => $dbName]);
        if ($unused) {
            $io->table(['Table', 'Index', 'Count'], $unused);
        } else {
            $io->text("Pas d’index inutilisés détectés (ou Performance Schema désactivé).");
        }

        // 4. Slow query log (optionnel)
        $slowLog = '/var/log/mysql/slow.log';
        if (file_exists($slowLog)) {
            $io->section("Slow query log (Top 5 requêtes)");
            $lines = shell_exec("mysqldumpslow -t 5 -s at $slowLog 2>/dev/null");
            $io->writeln($lines ?: "Pas de requêtes lentes détectées");
        } else {
            $io->note("Fichier slow log introuvable : $slowLog");
        }

        $io->success("Diagnostic terminé ✅");

        return Command::SUCCESS;
    }
}
