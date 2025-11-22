<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Renderer;

use Symfony\Component\HttpFoundation\Response;

interface PdfRendererInterface
{
    public function render(string $html, array $options): Response;
    public function supports(string $renderer): bool;
}