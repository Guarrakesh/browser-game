<?php

namespace App\Configuration\Building;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BuildingsConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('buildings');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->arrayPrototype()
                ->children()
                    ->scalarNode('max_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('min_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('cost_factor')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_population')->isRequired()->cannotBeEmpty()->end()
                    ->append($this->addRequiresSection())
                    ->arrayNode('base_cost')
                        ->children()
                            ->scalarNode('concrete')->cannotBeEmpty()->end()
                            ->scalarNode('metals')->cannotBeEmpty()->end()
                            ->scalarNode('circuits')->cannotBeEmpty()->end()
                            ->scalarNode('food')->cannotBeEmpty()->end()
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