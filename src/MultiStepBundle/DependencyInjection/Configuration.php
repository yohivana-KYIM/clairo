<?php

namespace App\MultiStepBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('multi_step');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('workflows')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->arrayNode('steps')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->scalarNode('class')->isRequired()->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}