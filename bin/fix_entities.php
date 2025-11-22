#!/usr/bin/env php
<?php
declare(strict_types=1);

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

$baseDir = dirname(__DIR__) . '/src/Entity';
if (!is_dir($baseDir)) {
    echo "❌ Directory not found: $baseDir\n";
    exit(1);
}

echo "🔍 Scanning directory: $baseDir\n\n";

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$processed = 0;

foreach ($rii as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php') continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    // Skip abstract or BaseFileEntity-based entities
    if (str_contains($content, 'abstract class')) continue;
    if (preg_match('/class\s+\w+\s+extends\s+BaseFileEntity/', $content)) {
        echo "🟨 Skipping (BaseFileEntity): {$file->getFilename()}\n";
        continue;
    }

    // Detect Doctrine entity
    if (!preg_match('/#\[ORM\\\\Entity/', $content)) continue;

    // Count and remove old ORM\Id attributes
    $idCount = preg_match_all('/#\[ORM\\\\Id\]/', $content, $matches);
    if ($idCount === 1) {
        echo "✅ OK (1 id) — {$file->getFilename()}\n";
        continue;
    }

    echo "⚙️ Fixing: {$file->getFilename()} (id count=$idCount)\n";
    $backup = $path . '.bak';
    copy($path, $backup);

    // 1️⃣ Remove all existing #[ORM\Id] lines
    $content = preg_replace('/\s*#\[ORM\\\\Id\]\s*/', '', $content);

    // 2️⃣ Find class opening to inject new synthetic block
    $lines = explode("\n", $content);
    $newLines = [];
    $inserted = false;

    foreach ($lines as $line) {
        $newLines[] = $line;
        if (!$inserted && str_contains($line, '{')) {
            $newLines[] = <<<PHP

    // 🧩 Synthetic ID added automatically
    #[ORM\\Id]
    #[ORM\\Column(type: 'string', length: 64)]
    private string \$id;

    public function __construct()
    {
        // Generate synthetic ID on construction
        \$this->computeSyntheticId();
    }

    private function computeSyntheticId(): void
    {
        // Build an MD5 hash of all object vars
        \$this->id = md5(json_encode(get_object_vars(\$this)));
    }

    public function getId(): string
    {
        return \$this->id;
    }

PHP;
            $inserted = true;
        }
    }

    $newContent = implode("\n", $newLines);
    file_put_contents($path, $newContent);

    echo "✅ Added synthetic ID (and removed $idCount old IDs) — backup: {$file->getFilename()}.bak\n";
    $processed++;
}

echo "\n✨ Done. $processed entities modified.\n";
