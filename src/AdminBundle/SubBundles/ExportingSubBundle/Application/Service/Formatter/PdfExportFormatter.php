<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsTaggedItem('app.admin_bundle.export_formatter')]
class PdfExportFormatter implements ExportFormatterInterface
{
    public function __construct(
        private readonly Pdf $pdfGenerator,
        private readonly Environment $twig,
        #[AutowireIterator('app.export_renderer')] private iterable $renderers
    ) {}

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function format(array $data, array $options = []): Response
    {
        $selectedColumns = $options['columns'] ?? [];
        $fieldFormats = $options['field_formats'] ?? [];
        $encoding = $options['encoding'] ?? 'UTF-8';
        $rendererType = $options['renderer'] ?? 'dompdf';
        $title = $options['title'] ?? 'Exported Report';
        $footer = $options['footer'] ?? '';
        $template = $options['template'] ?? 'export/pdf_export.html.twig';

        $data = $this->encodeData($data, $encoding);
        $data = $this->formatFields($data, $fieldFormats);
        $data = $this->filterAndOrderColumns($data, $selectedColumns);

        $html = $this->twig->render($template, [
            'title' => $title,
            'headers' => !empty($data) ? array_keys($data[0]) : [],
            'rows' => $data,
            'options' => $options, // si le template a besoin d'infos additionnelles
            'footer' => $footer,
        ]);

        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($rendererType)) {
                return $renderer->render($html, $options);
            }
        }

        $pdfContent = $this->pdfGenerator->getOutputFromHtml($html);

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => $this->getContentType(),
                'Content-Disposition' => 'attachment; filename="export.' . $this->getFileExtension() . '"'
            ]
        );
    }

    public function supports(string $format): bool
    {
        return $format === 'pdf';
    }

    public function getFileExtension(): string
    {
        return 'pdf';
    }

    public function getContentType(): string
    {
        return 'application/pdf';
    }

    public function encodeData(array $data, string $encoding): array
    {
        return array_map(function ($row) use ($encoding) {
            return array_map(function ($value) use ($encoding) {
                return mb_convert_encoding((string) $value, $encoding, 'UTF-8');
            }, $row);
        }, $data);
    }

    public function filterAndOrderColumns(array $data, array $selectedColumns = []): array
    {
        if (empty($selectedColumns)) {
            return $data;
        }

        return array_map(function ($row) use ($selectedColumns) {
            $filtered = [];
            foreach ($selectedColumns as $column) {
                $filtered[$column] = $row[$column] ?? null;
            }
            return $filtered;
        }, $data);
    }

    public function formatFields(array $data, array $fieldFormats = []): array
    {
        return array_map(function ($row) use ($fieldFormats) {
            foreach ($fieldFormats as $field => $callback) {
                if (isset($row[$field]) && is_callable($callback)) {
                    $row[$field] = $callback($row[$field]);
                }
            }
            return $row;
        }, $data);
    }

    public function getRenderers(): iterable
    {
        return $this->renderers;
    }

    public function setRenderers(iterable $renderers): void
    {
        $this->renderers = $renderers;
    }
}
