<?php

namespace App\AdminBundle\RenderingBundle\Infrastructure\Symfony\DependencyInjection;

namespace App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Symfony\DependencyInjection;

use App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Config\RenderingConfig;
use App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Symfony\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class RenderingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new RenderingConfig($this->processConfiguration(new Configuration(), $configs));
        $container->setParameter('rendering_bundle.strategy', $configuration->getRenderingStrategy());
        $container->setParameter('rendering_bundle.default_templates', $configuration->getDefaultTemplates());
    }
}
