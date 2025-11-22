<?php
namespace App\Security\FileSecurity\Interfaces;

interface FileSecurityCheckerInterface
{
    public function validate(array $file): bool;
}
