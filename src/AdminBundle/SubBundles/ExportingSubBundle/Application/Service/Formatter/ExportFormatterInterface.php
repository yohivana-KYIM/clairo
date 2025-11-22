<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Formatter;

use Symfony\Component\HttpFoundation\Response;

interface ExportFormatterInterface
{
    /**
     * Exports data into the desired format.
     */
    public function format(array $data, array $options = []): Response;

    /**
     * Checks if the formatter supports the given format.
     */
    public function supports(string $format): bool;

    /**
     * Defines the default file extension for the format.
     */
    public function getFileExtension(): string;

    /**
     * Defines the default Content-Type header for the format.
     */
    public function getContentType(): string;

    /**
     * Handles encoding transformation (UTF-8, Base64, URL-encoding).
     */
    public function encodeData(array $data, string $encoding): array;

    /**
     * Allows filtering & ordering specific columns.
     */
    public function filterAndOrderColumns(array $data, array $selectedColumns = []): array;

    /**
     * Formats specific field values (e.g., date formatting, rounding numbers).
     */
    public function formatFields(array $data, array $fieldFormats = []): array;
}
