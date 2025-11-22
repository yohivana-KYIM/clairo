<?php

namespace App\Service\EntityManagerServices;

use App\Entity\BaseFileEntity;
use App\Entity\DemandeTitreCirculation;
use App\Security\FileSecurity\Interfaces\FileSecurityCheckerInterface;
use App\Service\Utility\Interfaces\FileUploadServiceInterface;
use App\Traits\FileUploadTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DocumentPersonnelManagerService
{
    public function __construct(
        private readonly FileUploadServiceInterface $fileUploadService,
    ) {
    }

    public function handleFileUpload(
        UploadedFile $file,
        ?BaseFileEntity $currentFileEntity,
        string $uploadDirectory
    ): ?BaseFileEntity
    {
        if ($currentFileEntity && $currentFileEntity->getName()) {
            $this->fileUploadService->delete($uploadDirectory . '/' . $currentFileEntity->getName());
        }

        $newFileName = $this->fileUploadService->upload($file, $uploadDirectory);

        if ($currentFileEntity) {
            $currentFileEntity->setName($newFileName);
            $currentFileEntity->setOriginalName($file->getClientOriginalName());
        }

        return $currentFileEntity;
    }

    public function handlePersonalFormDatas(
        DemandeTitreCirculation $demandeTitreCirculation,
        FormInterface $form,
        Request $request
    ): array {
        $data = [
            'demande' => $demandeTitreCirculation,
            'duree' => $demandeTitreCirculation->getIntervention()->getDuree(),
            'nationalite' => $demandeTitreCirculation->getEtatCivil()->getNationalite(),
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ];

        $form->handleRequest($request);

        return $data;
    }
}
