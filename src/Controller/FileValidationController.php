<?php

namespace App\Controller;

use Exception;
use App\Security\FileSecurity\Interfaces\FileSecurityCheckerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FileValidationController extends AbstractController
{
    private readonly FileSecurityCheckerInterface $fileSecurityChecker;

    public function __construct(FileSecurityCheckerInterface $fileSecurityChecker)
    {
        $this->fileSecurityChecker = $fileSecurityChecker;
    }

    #[Route('/validate-file', name: 'validate_file', methods: ['POST', 'GET'])]
    public function validateFile(Request $request): JsonResponse
    {
        $fileData = [
            'name' => $request->request->get('name'),
            'size' => $request->request->get('size'),
            'tmp_name' => $request->files->get('tmp_name')->getPathname(),
        ];

        try {
            $this->fileSecurityChecker->validate($fileData);
            return new JsonResponse(['valid' => true]);
        } catch (Exception $e) {
            return new JsonResponse(['valid' => false, 'message' => $e->getMessage()], 400);
        }
    }
}