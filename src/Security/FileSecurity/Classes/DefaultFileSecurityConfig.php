<?php

namespace App\Security\FileSecurity\Classes;

use App\Security\FileSecurity\Interfaces\FileSecurityConfigInterface;
use App\Security\FileSecurity\Interfaces\VirusScannerInterface;

/**
 * @see config/packages/file_security.yaml for configurations
 * @see config/services/file_security_services.yml to declare services
 */
class DefaultFileSecurityConfig implements FileSecurityConfigInterface
{

    const MAX_FILE_SIZE_10MB = 5 * 1024 * 1024; // 5 Mo

    private readonly int $maxFileSize;
    private readonly array $allowedTypes;
    private readonly VirusScannerInterface $virusScanner;

    public function __construct(int $maxFileSize = self::MAX_FILE_SIZE_10MB, array $allowedTypes = [], ?VirusScannerInterface $virusScanner = null)
    {
        $this->maxFileSize = $maxFileSize;
        $this->allowedTypes = $allowedTypes;
        $this->virusScanner = $virusScanner;
    }

    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function getAllowedTypes(): array
    {
        return $this->allowedTypes;
    }

    public function getVirusScanner(): ?VirusScannerInterface
    {
        return $this->virusScanner;
    }
}