<?php

namespace App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Symfony\DependencyInjection;

use App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Config\SortingConfig;
use App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Symfony\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SortingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new SortingConfig($this->processConfiguration(new Configuration(), $configs));
        $container->setParameter('sorting_bundle.strategy', $configuration->getSortingStrategy());

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
