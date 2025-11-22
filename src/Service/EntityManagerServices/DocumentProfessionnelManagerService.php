<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DocumentPersonnel;
use App\Entity\DocumentProfessionnel;
use App\Entity\Gies0;
use App\Entity\Gies1;
use App\Entity\Gies2;
use App\Entity\Atex0;
use App\Entity\AutreDocument;
use App\Entity\User;
use App\Service\Utility\Classes\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DocumentProfessionnelManagerService
{
    private readonly string $uploadDirectory;

    public function __construct(private readonly ParameterBagInterface $parameterBag, private readonly EntityManagerInterface $entityManager, private readonly FileUploadService $fileUploadService)
    {
        $this->uploadDirectory = $this->parameterBag->get('document_professionnel_directory');
    }

    public function handleFileUpload(UploadedFile $file, $entity): void
    {
        // Use FileUploadService to handle file upload
        $fileName = $this->fileUploadService->upload($file, $this->uploadDirectory);

        // Update the entity with file details
        $entity->setName($fileName);
        $entity->setOriginalName($file->getClientOriginalName());
        $this->entityManager->persist($entity);
    }

    public function processDocument(
        DocumentProfessionnel $document,
        array $files
    ): void {
        foreach ($files as $field => $uploadedFile) {
            if ($uploadedFile instanceof UploadedFile) {
                $entity = $this->createEntityForField($field);
                $this->handleFileUpload($uploadedFile, $entity);
                $setter = 'set' . ucfirst($field);
                $document->$setter($entity);
            }
        }

        $document->setSubmited(true);
        $this->entityManager->persist($document);
        $this->entityManager->flush();
    }

    private function createEntityForField(string $field): object
    {
        return match ($field) {
            'gies0' => new Gies0(),
            'gies1' => new Gies1(),
            'gies2' => new Gies2(),
            'atex0' => new Atex0(),
            'autre' => new AutreDocument(),
            default => throw new InvalidArgumentException("Unknown field: $field"),
        };
    }

    public function extractUploadedFiles($form): array
    {
        return [
            'gies0' => $form->get('gies0')->getData(),
            'gies1' => $form->get('gies1')->getData(),
            'gies2' => $form->get('gies2')->getData(),
            'atex0' => $form->get('atex0')->getData(),
            'autre' => $form->get('autre')->getData(),
        ];
    }

    public function getAllDocuments(): array
    {
        return $this->entityManager->getRepository(DocumentProfessionnel::class)->findAll();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getFormDatas(Request $request, User $user): array {
        $demandeTitreCirculation= $user->getDemandes()->last();
        $docPerso = $demandeTitreCirculation->getDocpersonnel() ?? $this->getEntityManager()->getRepository(DocumentPersonnel::class)->find($request->get('id'));
        return [
            'demande' => $demandeTitreCirculation,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'infoComplementaire' =>  $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $docPerso,
        ];
    }
}
