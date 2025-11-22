<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection;

use App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Config\SearchFilterConfig;
use App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection\Compiler\SearchableClassPass;
use App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection\Compiler\SearchableFilterableClassPass;
use App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Symfony\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class SearchFilterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new SearchFilterConfig($this->processConfiguration(new Configuration(), $configs));
        $container->addCompilerPass(new SearchableClassPass());
        $container->addCompilerPass(new SearchableFilterableClassPass());
        $container->setParameter('search_filter_bundle.strategy', $configuration->getSearchStrategy());
        $container->setParameter('search_filter_bundle.filters', $configuration->getFilters());
    }
}
