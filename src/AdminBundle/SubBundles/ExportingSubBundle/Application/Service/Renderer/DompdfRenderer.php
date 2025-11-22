<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Renderer;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;

#[AsTaggedItem('app.admin_bundle.export_renderer')]
class DompdfRenderer implements PdfRendererInterface
{
    public function render(string $html, array $options): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', $options['font'] ?? 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->render();

        return new Response($dompdf->output(), 200, ['Content-Type' => 'application/pdf']);
    }

    public function supports(string $renderer): bool
    {
        return $renderer === 'dompdf';
    }
}