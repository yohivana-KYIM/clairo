<?php

// src/Controller/AjaxFileValidationController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxFileValidationController extends AbstractController
{
    #[Route('/ajax/validate-upload', name: 'ajax_validate_upload', methods: ['POST'])]
    public function validateUpload(Request $request): Response
    {
        $errors = [];
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');
        $key  = $request->request->get('key', 'unknown_field');

        if (!$file instanceof UploadedFile) {
            return $this->json([
                'success' => false,
                'errors'  => ['Aucun fichier reçu.']
            ], 400);
        }

        // 💡 On réutilise ta fonction
        $hasErrors = $this->validateFile($file, $errors, $key);

        return $this->json([
            'success' => !$hasErrors,
            'errors'  => $errors[$key] ?? []
        ]);
    }

    private function validateFile(UploadedFile $file, array &$errors, $key): bool|string
    {
        $allowedMime = [
            'application/pdf',
            'image/jpeg','image/png','image/gif','image/bmp','image/webp',
            'image/tiff','image/heic','image/heif'
        ];
        $allowedExt = ['pdf','jpg','jpeg','jpe','png','gif','bmp','webp','tif','tiff','heic','heif'];
        $maxBytes   = 5 * 1024 * 1024;

        $errors[$key] = [];

        if ($file->getSize() > $maxBytes) {
            $errors[$key][] = "File too large (>5MB)";
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExt, true)) {
            $errors[$key][] = "Extension not allowed";
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());
        if (!in_array($realMime, $allowedMime, true)) {
            $errors[$key][] = "Real MIME type not allowed ($realMime)";
        }

        return !empty($errors[$key]);
    }
}

