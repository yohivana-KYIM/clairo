<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use ReflectionClass;
use App\AdminBundle\SubBundles\SearchFilterBundle\Domain\Annotation\Searchable;
use App\AdminBundle\SubBundles\SearchFilterBundle\Domain\Annotation\Filterable;

class SearchableFilterableClassPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $searchableServices = [];
        $filterableServices = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if (!$class || !class_exists($class)) continue;

            $reflection = new ReflectionClass($class);
            $searchAttributes = $reflection->getAttributes(Searchable::class);
            $filterAttributes = $reflection->getAttributes(Filterable::class);

            if (!empty($searchAttributes)) {
                $searchableServices[$class] = $searchAttributes[0]->newInstance()->methods;
            }

            if (!empty($filterAttributes)) {
                $filterableServices[$class] = $filterAttributes[0]->newInstance()->methods;
            }
        }

        $container->setParameter('search_filter_bundle.searchable_classes', $searchableServices);
        $container->setParameter('search_filter_bundle.filterable_classes', $filterableServices);
    }
}
