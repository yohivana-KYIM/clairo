<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Template\VariableFormatter;
use DateMalformedStringException;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsTaggedItem('app.admin_bundle.export_formatter')]
class JsonExportFormatter implements ExportFormatterInterface
{
    public function __construct(private readonly Environment $twig, private readonly VariableFormatter $variableFormatter)
    {

    }

    /**
     * @throws DateMalformedStringException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function format(array $data, array $options = []): Response
    {
        $prettyPrint = $options['pretty_print'] ?? false;
        $flags = $prettyPrint ? JSON_PRETTY_PRINT : 0;
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

        return new Response(json_encode($data, $flags), 200, ['Content-Type' => $this->getContentType()]);
    }

    public function supports(string $format): bool
    {
        return $format === 'json';
    }

    public function getFileExtension(): string
    {
        return 'json';
    }

    public function getContentType(): string
    {
        return 'application/json';
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

    /**
     * @throws DateMalformedStringException
     */
    public function formatFields(array $data, array $fieldFormats = []): array
    {
        foreach ($data as &$row) {
            foreach ($fieldFormats as $field => $format) {
                if (isset($row[$field])) {
                    $row[$field] = match ($format) {
                        'uppercase' => strtoupper($row[$field]),
                        'lowercase' => strtolower($row[$field]),
                        'date:Y-m-d' => (new DateTime($row[$field]))->format('Y-m-d'),
                        'currency' => number_format((float) $row[$field], 2) . ' $',
                        default => $row[$field],
                    };
                }
            }
        }
        return $data;
    }
}
