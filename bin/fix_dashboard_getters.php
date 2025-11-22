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

echo "🔍 Scanning Dashboard entities for scalar getters in: $baseDir\n\n";

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$processed = 0;

foreach ($rii as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php') continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    if (!str_contains($content, 'ORM\\Entity')) continue;

    $backup = $path . '.bak';
    if (!file_exists($backup)) {
        copy($path, $backup);
    }

    // Detect simple scalar getters with direct return
    $pattern = '/public\s+function\s+(get[A-Z]\w*)\s*\(\)\s*:\s*(int|float|string|bool)\s*\{\s*return\s+\$this->([A-Za-z_]\w*)\s*;\s*\}/m';
    $count = 0;

    $newContent = preg_replace_callback($pattern, function ($m) use (&$replacements) {
        $method = $m[1];
        $type = $m[2];
        $property = $m[3];
        $replacements++;

        $default = match ($type) {
            'int' => '0',
            'float' => '0.0',
            'bool' => 'false',
            'string' => "''",
            default => 'null',
        };

        return "public function {$method}(): {$type} { return \$this->{$property} ?? {$default}; }";
    }, $content, -1, $count);

    if ($count > 0) {
        file_put_contents($path, $newContent);
        echo "✅ {$file->getFilename()} — patched {$count} scalar getter(s)\n";
        $processed++;
    }
}

echo "\n✨ Done. Patched $processed Dashboard entities.\n";
