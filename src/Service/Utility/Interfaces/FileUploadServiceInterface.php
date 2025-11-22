<?php

namespace App\Service\Utility\Interfaces;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploadServiceInterface
{
    public function upload(UploadedFile $file, string $uploadDirectory): string;

    public function delete(string $filePath): void;

    public function validate(UploadedFile $file): void;
}