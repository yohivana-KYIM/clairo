<?php

namespace App\Controller;

use App\Entity\DocumentProfessionnel;
use App\Entity\User;
use App\Form\DocumentProfessionnelType;
use App\Service\EntityManagerServices\DocumentProfessionnelManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/document/professionnel')]
class DocumentProfessionnelController extends AbstractController
{
    public function __construct(private readonly DocumentProfessionnelManagerService $managerService)
    {
    }

    #[Route('/', name: 'app_document_professionnel_index', methods: ['GET'])]
    public function index(): Response
    {
        $documents = $this->managerService->getAllDocuments();
        return $this->render('document_professionnel/index.html.twig', [
            'document_professionnels' => $documents,
        ]);
    }

    #[Route('/new', name: 'app_document_professionnel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $document = new DocumentProfessionnel();
        return $this->handleForm($request, $document, $entityManager, 'document_professionnel/new.html.twig');
    }

    #[Route('/{id}', name: 'app_document_professionnel_show', methods: ['GET'])]
    public function show(DocumentProfessionnel $document): Response
    {
        return $this->render('document_professionnel/show.html.twig', [
            'document_professionnel' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_document_professionnel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DocumentProfessionnel $document, EntityManagerInterface $entityManager): Response
    {
        return $this->handleForm($request, $document, $entityManager, 'document_professionnel/edit.html.twig');
    }

    #[Route('/{id}', name: 'app_document_professionnel_delete', methods: ['POST'])]
    public function delete(Request $request, DocumentProfessionnel $document): Response
    {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $this->managerService->getEntityManager()->remove($document);
            $this->managerService->getEntityManager()->flush();
        }

        return $this->redirectToRoute('app_document_professionnel_index');
    }

    private function handleForm(Request $request, DocumentProfessionnel $document, EntityManagerInterface $entityManager, string $template): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $form = $this->createForm(DocumentProfessionnelType::class, $document);
        $demandeTitreCirculation = $user->getDemandes()->last();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFiles = $this->managerService->extractUploadedFiles($form);
            $this->managerService->processDocument($document, $uploadedFiles);

            return $this->redirectToRoute('app_recap_index', [], Response::HTTP_SEE_OTHER);
        }
        $demandeTitreCirculation->setDocumentprofessionnel($document);
        $entityManager->persist($demandeTitreCirculation);
        $entityManager->flush();

        return $this->render($template, [
                'form' => $form,
                'documentProfessionnel' => $document
            ] + $this->managerService->getFormDatas($request, $user)
        );
    }
}
