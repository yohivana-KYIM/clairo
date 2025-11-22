<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Renderer;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;

#[AsTaggedItem('app.admin_bundle.export_renderer')]
class WkhtmltopdfRenderer implements PdfRendererInterface
{
    private Pdf $wkhtmltopdf;

    public function __construct()
    {
        $this->wkhtmltopdf = new Pdf('/usr/local/bin/wkhtmltopdf');
    }

    public function render(string $html, array $options): Response
    {
        $pdf = $this->wkhtmltopdf->getOutputFromHtml($html, [
            'margin-top' => $options['margins']['top'] ?? 10,
            'margin-bottom' => $options['margins']['bottom'] ?? 10,
            'margin-left' => $options['margins']['left'] ?? 10,
            'margin-right' => $options['margins']['right'] ?? 10,
        ]);

        return new Response($pdf, 200, ['Content-Type' => 'application/pdf']);
    }

    public function supports(string $renderer): bool
    {
        return $renderer === 'wkhtmltopdf';
    }
}