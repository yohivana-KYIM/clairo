<?php

namespace App\AdminBundle\Infrastructure\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AdminEntityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('admin_bundle.repository')) {
            return;
        }

        $definition = $container->getDefinition('admin_bundle.repository');
        $entities = $container->getParameter('admin_bundle.entities');

        foreach ($entities as $entityClass) {
            $definition->addMethodCall('registerEntity', [new Reference($entityClass)]);
        }
    }
}
