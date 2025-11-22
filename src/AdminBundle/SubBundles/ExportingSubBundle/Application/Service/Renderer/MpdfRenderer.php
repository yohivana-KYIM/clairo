<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Renderer;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;

#[AsTaggedItem('app.admin_bundle.export_renderer')]
class MpdfRenderer implements PdfRendererInterface
{
    /**
     * @throws MpdfException
     */
    public function render(string $html, array $options): Response
    {
        $mpdf = new Mpdf([
            'margin_top' => $options['margins']['top'] ?? 10,
            'margin_bottom' => $options['margins']['bottom'] ?? 10,
            'margin_left' => $options['margins']['left'] ?? 10,
            'margin_right' => $options['margins']['right'] ?? 10,
        ]);

        $mpdf->WriteHTML($html);
        return new Response($mpdf->Output('', 'S'), 200, ['Content-Type' => 'application/pdf']);
    }

    public function supports(string $renderer): bool
    {
        return $renderer === 'mpdf';
    }
}