<?php

namespace App\Controller;

use App\Entity\DocumentPersonnel;
use App\Entity\User;
use App\Service\Factory\BaseFileEntityFactory;
use App\Form\DocumentPersonnelType;
use App\Repository\DocumentPersonnelRepository;
use App\Service\EntityManagerServices\DocumentPersonnelManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/document/personnel')]
class DocumentPersonnelController extends AbstractController
{
    public function __construct(private readonly DocumentPersonnelManagerService $managerService, private readonly BaseFileEntityFactory $baseFileEntityFactory)
    {
    }

    #[Route('/', name: 'app_document_personnel_index', methods: ['GET'])]
    public function index(DocumentPersonnelRepository $repository): Response
    {
        return $this->render('document_personnel/index.html.twig', [
            'document_personnels' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_document_personnel_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_document_personnel_edit', methods: ['GET', 'POST'])]
    public function form(
        Request $request,
        EntityManagerInterface $entityManager,
        ?DocumentPersonnel $documentPersonnel = null
    ): Response {
        $documentPersonnel ??= new DocumentPersonnel();
        $form = $this->createForm(DocumentPersonnelType::class, $documentPersonnel);
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $user->getDemandes()->last();

        $formData = $this->managerService->handlePersonalFormDatas(
            $demandeTitreCirculation,
            $form,
            $request
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadDirectory = $this->getParameter('document_personnel_directory');

            foreach ($form->all() as $fieldName => $field) {
                $uploadedFile = $field->getData();
                if ($uploadedFile instanceof UploadedFile) {
                    $currentFieldEntity = $documentPersonnel->{'get' . ucfirst($fieldName)}();
                    if (empty($currentFieldEntity)) {
                         $currentFieldEntity = $this->baseFileEntityFactory->createFromFieldName(DocumentPersonnel::class, $fieldName);
                    }
                    $currentFieldEntity = $this->managerService->handleFileUpload($uploadedFile, $currentFieldEntity, $uploadDirectory);
                    $entityManager->persist($currentFieldEntity);
                    $documentPersonnel->{'set' . ucfirst($fieldName)}($currentFieldEntity);
                }
            }

            $entityManager->persist($documentPersonnel);
            $entityManager->flush();
            $demandeTitreCirculation->setDocpersonnel($documentPersonnel);
            $entityManager->persist($documentPersonnel);
            $entityManager->flush();

            $route = $demandeTitreCirculation->getDocumentprofessionnel()
                ? 'app_document_professionnel_edit'
                : 'app_document_professionnel_new';
            return $this->redirectToRoute($route, ['id' => $documentPersonnel->getId()]);
        }

        return $this->render('document_personnel/_form.html.twig', [
            'documentPersonnel' => $documentPersonnel,
            'form' => $form,
            ...$formData,
        ]);
    }

    #[Route('/{id}', name: 'app_document_personnel_delete', methods: ['POST'])]
    public function delete(Request $request, DocumentPersonnel $documentPersonnel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $documentPersonnel->getId(), $request->request->get('_token'))) {
            $entityManager->remove($documentPersonnel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_document_personnel_index');
    }
}
