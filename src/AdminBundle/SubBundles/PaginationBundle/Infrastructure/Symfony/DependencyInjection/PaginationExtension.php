<?php

namespace App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Symfony\DependencyInjection;

use App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Config\PaginationConfig;
use App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Symfony\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PaginationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new PaginationConfig($this->processConfiguration(new Configuration(), $configs));
        $container->setParameter('pagination_bundle.strategy', $configuration->getPaginationStrategy());
        $container->setParameter('pagination_bundle.items_per_page', $configuration->getItemsPerPage());
    }
}
