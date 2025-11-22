<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;

#[AsTaggedItem('app.admin_bundle.export_formatter')]
class XmlExportFormatter implements ExportFormatterInterface
{
    public function format(array $data, array $options = []): Response
    {
        $rootElement = $options['root_element'] ?? 'root';
        $selectedColumns = $options['columns'] ?? [];
        $fieldFormats = $options['field_formats'] ?? [];
        $encoding = $options['encoding'] ?? 'UTF-8';

        $data = $this->encodeData($data, $encoding);
        $data = $this->formatFields($data, $fieldFormats);
        $data = $this->filterAndOrderColumns($data, $selectedColumns);

        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"$encoding\"?><$rootElement/>");

        foreach ($data as $item) {
            $entry = $xml->addChild('entry');
            foreach ($item as $key => $value) {
                // Sécurise les clés XML et encodage spécial
                $safeKey = preg_replace('/[^a-z0-9_]/i', '_', $key);
                $entry->addChild($safeKey, htmlspecialchars((string) $value));
            }
        }

        return new Response($xml->asXML(), 200, [
            'Content-Type' => $this->getContentType(),
            'Content-Disposition' => 'attachment;filename="export.' . $this->getFileExtension() . '"',
        ]);
    }

    public function supports(string $format): bool
    {
        return $format === 'xml';
    }

    public function getFileExtension(): string
    {
        return 'xml';
    }

    public function getContentType(): string
    {
        return 'application/xml';
    }

    public function encodeData(array $data, string $encoding): array
    {
        return array_map(function ($row) use ($encoding) {
            return array_map(function ($value) use ($encoding) {
                return mb_convert_encoding($value, $encoding, 'UTF-8');
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
}
