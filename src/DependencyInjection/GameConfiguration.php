<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class GameConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('game');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode

            ->children()
            ->arrayNode('buildings')
            ->isRequired()
            ->useAttributeAsKey('name')
            ->fixXmlConfig('building')
            ->prototype('array')
                ->children()
                    ->scalarNode('max_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('min_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('cost_factor')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_population')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('hourly_production')->end()
                    ->scalarNode('increase_factor')->end()
                    ->scalarNode('max_storage')->end()
                    ->append($this->addRequiresSection())
                    ->arrayNode('base_cost')
                        ->isRequired()
                        ->children()
                            ->scalarNode('concrete')->cannotBeEmpty()->end()
                            ->scalarNode('metals')->cannotBeEmpty()->end()
                            ->scalarNode('circuits')->cannotBeEmpty()->end()
                            ->scalarNode('food')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->end();
        return $treeBuilder;



    }

    private function addRequiresSection(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('requires');


        $node = $treeBuilder->getRootNode()
            ->isRequired()
            ->scalarPrototype()->end();


        return $node;
    }
}