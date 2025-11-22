<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Config;

class ExportConfig
{
    private string $encoding;
    private array $formatConfigs;

    public function __construct(array $config = [])
    {
        $this->encoding = $config['encoding'] ?? 'utf-8';
        $this->formatConfigs = $config['formats'] ?? [];
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getFormatConfig(string $format): array
    {
        return $this->formatConfigs[$format] ?? [];
    }
}
