<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Renderer;

use FPDF;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;

#[AsTaggedItem('app.admin_bundle.export_renderer')]
class FpdfRenderer implements PdfRendererInterface
{
    public function render(string $html, array $options): Response
    {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont($options['font'] ?? 'Arial', '', 12);
        $pdf->MultiCell(0, 10, strip_tags($html));

        ob_start();
        $pdf->Output();
        return new Response(ob_get_clean(), 200, ['Content-Type' => 'application/pdf']);
    }

    public function supports(string $renderer): bool
    {
        return $renderer === 'fpdf';
    }
}
