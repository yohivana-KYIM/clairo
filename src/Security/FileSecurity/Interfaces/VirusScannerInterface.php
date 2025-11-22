<?php
namespace App\Security\FileSecurity\Interfaces;

interface VirusScannerInterface
{
    public function scan(string $filePath): bool;
}
