<?php

namespace App\Service\Utility\Classes;

use Exception;
use App\Security\FileSecurity\Interfaces\FileSecurityCheckerInterface;
use App\Service\Utility\Interfaces\FileUploadServiceInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService implements FileUploadServiceInterface
{
    private readonly FileSecurityCheckerInterface $fileSecurityChecker;

    public function __construct(FileSecurityCheckerInterface $fileSecurityChecker)
    {
        $this->fileSecurityChecker = $fileSecurityChecker;
    }

    public function upload(UploadedFile $file, string $uploadDirectory): string
    {
        // Validate the file using FileSecurityChecker
        $this->validate($file);

        // Generate a unique file name
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        // Move the file to the upload directory
        try {
            $file->move($uploadDirectory, $fileName);
        } catch (Exception $e) {
            throw new RuntimeException('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
        }

        return $fileName;
    }

    public function delete(string $filePath): void
    {
        if (file_exists($filePath) && !unlink($filePath)) {
            throw new RuntimeException('Erreur lors de la suppression du fichier : ' . $filePath);
        }
    }

    public function validate(UploadedFile $file): void
    {
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'tmp_name' => $file->getPathname(),
        ];

        try {
            $this->fileSecurityChecker->validate($fileData);
        } catch (Exception $e) {
            throw new RuntimeException('Validation du fichier échouée : ' . $e->getMessage());
        }
    }
}