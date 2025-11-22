<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service;

use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\Exportable;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportConfigResolver
{
    private array $globalConfig;
    private EntityManagerInterface $entityManager;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $entityManager)
    {
        $this->globalConfig = $params->get('exporting_sub_bundle');
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ReflectionException
     */
    public function getEntityExportConfig(string $entityClass): array
    {
        $reflection = new ReflectionClass($entityClass);
        $attributes = $reflection->getAttributes(Exportable::class);

        $entityConfig = $attributes ? $attributes[0]->newInstance() : null;
        return [
            'formats' => $entityConfig?->formats ?? $this->globalConfig['formats'],
            'template' => $entityConfig?->template ?? null,
            'encoding' => $entityConfig?->encoding ?? $this->globalConfig['encoding'],
            'variableDetection' => array_merge($this->globalConfig['variable_detection'], $entityConfig?->variableDetection ?? []),
            'formatConfig' => array_merge_recursive($this->globalConfig['formats'], $entityConfig?->formatConfig ?? []),
        ];
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
