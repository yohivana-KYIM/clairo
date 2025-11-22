<?php

namespace App\Security\FileSecurity\Interfaces;

interface FileSecurityConfigInterface
{
    public function getMaxFileSize(): int;
    public function getAllowedTypes(): array;
    public function getVirusScanner(): ?VirusScannerInterface;
}