<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Template\VariableFormatter;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[AsTaggedItem('app.admin_bundle.export_formatter')]
class CsvExportFormatter implements ExportFormatterInterface
{
    public function __construct(private readonly Environment $twig, private readonly VariableFormatter $variableFormatter)
    {

    }
    public function format(array $data, array $options = []): Response
    {
        $delimiter = $options['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? '"';
        $escapeChar = $options['escape_char'] ?? "\\";

        $encoding = $options['encoding'] ?? 'UTF-8';
        $columns = $options['columns'] ?? [];
        $fieldFormats = $options['fieldFormats'] ?? [];

        $template = $options['template'] ?? null;
        if ($template) {
            $content = $this->twig->render($template, ['data' => $data]);
            $content = $this->variableFormatter->replaceVariables($content, $data);
            return new Response($content, 200, ['Content-Type' => 'text/csv']);
        }

        $data = $this->filterAndOrderColumns($data, $columns);
        $data = $this->formatFields($data, $fieldFormats);
        $data = $this->encodeData($data, $encoding);

        return new StreamedResponse(function () use ($data, $delimiter, $enclosure, $escapeChar) {
            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($data[0] ?? []), $delimiter, $enclosure, $escapeChar);

            foreach ($data as $row) {
                fputcsv($output, $row, $delimiter, $enclosure, $escapeChar);
            }
            fclose($output);
        }, 200, ['Content-Type' => $this->getContentType()]);
    }

    public function supports(string $format): bool
    {
        return $format === 'csv';
    }

    public function getFileExtension(): string
    {
        return 'csv';
    }

    public function getContentType(): string
    {
        return 'text/csv';
    }

    public function encodeData(array $data, string $encoding): array
    {
        return match ($encoding) {
            'base64' => array_map(fn($row) => array_map('base64_encode', $row), $data),
            'url' => array_map(fn($row) => array_map('urlencode', $row), $data),
            default => $data,
        };
    }

    public function filterAndOrderColumns(array $data, array $selectedColumns = []): array
    {
        if (empty($selectedColumns)) {
            return $data;
        }

        return array_map(fn($row) => array_intersect_key($row, array_flip($selectedColumns)), $data);
    }

    public function formatFields(array $data, array $fieldFormats = []): array
    {
        foreach ($data as &$row) {
            foreach ($fieldFormats as $field => $format) {
                if (isset($row[$field])) {
                    $row[$field] = match ($format) {
                        'uppercase' => strtoupper($row[$field]),
                        'lowercase' => strtolower($row[$field]),
                        'date:Y-m-d' => (new \DateTime($row[$field]))->format('Y-m-d'),
                        'currency' => number_format((float) $row[$field], 2) . ' $',
                        default => $row[$field],
                    };
                }
            }
        }
        return $data;
    }
}
