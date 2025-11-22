<?php

namespace App\AdminBundle\Infrastructure\Symfony\DependencyInjection;

use App\AdminBundle\Infrastructure\Config\AdminConfig;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new AdminConfig($this->processConfiguration(new Configuration(), $configs));
        $container->setParameter('admin_bundle.entities', $configuration->getEntities());
        $container->setParameter('admin_bundle.sorting_strategy', $configuration->getSortingStrategy());
        $container->setParameter('admin_bundle.rendering_strategy', $configuration->getRenderingStrategy());

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
