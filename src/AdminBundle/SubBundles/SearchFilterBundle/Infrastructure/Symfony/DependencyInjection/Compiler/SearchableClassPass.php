<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ReflectionClass;
use App\AdminBundle\SubBundles\SearchFilterBundle\Domain\Annotation\Searchable;

class SearchableClassPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $searchableServices = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if (!$class || !class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            $attributes = $reflection->getAttributes(Searchable::class);

            if (!empty($attributes)) {
                $searchableServices[$class] = $attributes[0]->newInstance()->methods;
            }
        }

        $container->setParameter('search_filter_bundle.searchable_classes', $searchableServices);
    }
}
