<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    // Directories to refactor
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // Things to skip (build artifacts, vendor, generated code, migrations, etc.)
    $rectorConfig->skip([
        __DIR__ . '/var',
        __DIR__ . '/vendor',
        __DIR__ . '/node_modules',
        __DIR__ . '/public/build',
        __DIR__ . '/public/bundles',
        __DIR__ . '/migrations',      // keep migrations stable
        __DIR__ . '/bin',
    ]);

    // Performance + determinism
    $rectorConfig->parallel();                       // speed up on multi-core
    $rectorConfig->cacheDirectory(__DIR__ . '/var/rector');

    // Import FQCNs (but don't shorten class names in code)
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    // --- Optional: better type inference for Symfony services ---
    // If you warm the container before running Rector, point to the XML here:
    // $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');

    // Target language level + curated rule sets
    $rectorConfig->sets([
        // PHP
        SetList::PHP_83,
        LevelSetList::UP_TO_PHP_83,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::CODING_STYLE,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::DEAD_CODE,
        SetList::NAMING,

        // Symfony 7.3 (and generic Symfony code-quality)
        SymfonySetList::SYMFONY_73,
        SymfonySetList::SYMFONY_CODE_QUALITY,

        // Doctrine (code quality + DBAL 3 rules; ORM version-specific sets in Rector
        // are older, so prefer code-quality instead of forcing 2.9 migrations)
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_DBAL_40,

        // PHPUnit 9.x code-quality (do NOT auto-upgrade to PHPUnit 10 if you’re on 9.5)
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
    ]);
};
