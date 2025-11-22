<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ReflectionClass;
use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\Exportable;

class ExportableClassPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $exportableServices = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if (!$class || !class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            $attributes = $reflection->getAttributes(Exportable::class);

            if (!empty($attributes)) {
                $exportableServices[$class] = $attributes[0]->newInstance()->formats;
            }
        }

        $container->setParameter('exporting_sub_bundle.exportable_classes', $exportableServices);
    }
}
