#!/usr/bin/env php
<?php
declare(strict_types=1);

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

$baseDir = dirname(__DIR__) . '/src/Entity/Dashboard';
if (!is_dir($baseDir)) {
    echo "❌ Directory not found: $baseDir\n";
    exit(1);
}

echo "🔍 Scanning Dashboard entities in: $baseDir\n\n";

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$processed = 0;

foreach ($rii as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php') continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    // only modify Doctrine entities
    if (!str_contains($content, 'ORM\\Entity')) continue;

    $backup = $path . '.bak';
    if (!file_exists($backup)) {
        copy($path, $backup);
    }

    // Regex: match numeric properties (int|float) without default (= ...)
    $pattern = '/private\s+(int|float)\s+\$([A-Za-z_][A-Za-z0-9_]*)\s*;(\r?\n)/';
    $count = 0;

    $newContent = preg_replace_callback($pattern, function ($m) use (&$count) {
        $type = $m[1];
        $name = $m[2];
        $count++;
        $default = $type === 'float' ? '0.0' : '0';
        // Convert to nullable type with default value
        return "private ?{$type} \${$name} = {$default};{$m[3]}";
    }, $content, -1, $count);

    if ($count > 0) {
        file_put_contents($path, $newContent);
        echo "✅ {$file->getFilename()} — patched {$count} numeric field(s)\n";
        $processed++;
    }
}

echo "\n✨ Done. Patched $processed Dashboard entities.\n";
