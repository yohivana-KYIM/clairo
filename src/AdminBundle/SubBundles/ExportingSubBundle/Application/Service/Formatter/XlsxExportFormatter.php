<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpFoundation\Response;

#[AsTaggedItem('app.admin_bundle.export_formatter')]
class XlsxExportFormatter implements ExportFormatterInterface
{
    public function format(array $data, array $options = []): Response
    {
        $sheetName = $options['sheet_name'] ?? 'Sheet1';
        $selectedColumns = $options['columns'] ?? [];
        $fieldFormats = $options['field_formats'] ?? [];
        $encoding = $options['encoding'] ?? 'UTF-8';

        $data = $this->encodeData($data, $encoding);
        $data = $this->formatFields($data, $fieldFormats);
        $data = $this->filterAndOrderColumns($data, $selectedColumns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName);

        if (!empty($data)) {
            $sheet->fromArray(array_merge([array_keys($data[0])], $data));
        }

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        return new Response(ob_get_clean(), 200, [
            'Content-Type' => $this->getContentType(),
            'Content-Disposition' => 'attachment;filename="export.' . $this->getFileExtension() . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function supports(string $format): bool
    {
        return $format === 'xlsx';
    }

    public function getFileExtension(): string
    {
        return 'xlsx';
    }

    public function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
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
