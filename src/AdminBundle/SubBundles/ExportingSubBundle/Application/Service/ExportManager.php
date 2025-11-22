<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service;

use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Logging\ExportLogger;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Security\ExportPermissionChecker;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ExportManager
{
    private iterable $formatters;

    public function __construct(
        #[AutowireIterator('app.export_formatter')] iterable $formatters,
        private readonly ExportConfigResolver $configResolver,
        private readonly Environment $twig,
        private readonly ExportPermissionChecker $permissionChecker,
        private readonly ExportLogger $exportLogger
    )
    {
        $this->formatters = $formatters;
    }

    /**
     * @throws Exception
     */
    public function export(string $entityClass, string $format, array $data, array $options = []): Response
    {
        if (!$this->permissionChecker->isExportAllowed($entityClass)) {
            throw new \Exception("Export is not allowed for this entity.");
        }

        $entityConfig = $this->configResolver->getEntityExportConfig($entityClass);

        if (!in_array($format, $entityConfig['formats'])) {
            throw new Exception("Export format '$format' is not allowed for $entityClass");
        }

        $options = array_merge($entityConfig['formatConfig'][$format] ?? [], $options);

        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($format)) {
                if ($entityConfig['template']) {
                    $data = $this->twig->render($entityConfig['template'], ['data' => $data]);
                }
                $this->exportLogger->logExport($entityClass, $format);
                return $formatter->format($data, $options);
            }
        }

        throw new Exception("Unsupported export format: $format");
    }
}