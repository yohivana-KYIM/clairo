<?php

namespace App\Controller;

use App\Service\Pdf\PdfGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pdf', name: 'app_pdf_')]
class PdfController extends AbstractController
{
    private readonly PdfGeneratorService $pdfGenerator;

    public function __construct(PdfGeneratorService $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    #[Route('/{id}', name: 'generate', methods: ['GET'])]
    public function generatePdf(int $id): Response
    {
        $pdfData = $this->pdfGenerator->preparePdfData($id);

        $html = $this->renderView('pdf/index.html.twig', $pdfData);
        $filename = 'OrderSummary';

        return $this->pdfGenerator->generatePdfFromHtml($html, $filename);
    }
}
