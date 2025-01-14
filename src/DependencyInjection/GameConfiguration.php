<?php

namespace App\DependencyInjection;

use PhpParser\Node;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class GameConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('game');

        $rootNode = $treeBuilder->getRootNode();

        $children = $rootNode

            ->children();

        $children
            ->arrayNode('buildings')
            ->isRequired()
            ->useAttributeAsKey('name')
            ->fixXmlConfig('building')

            ->arrayPrototype()
                ->children()
                    ->scalarNode('max_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('min_level')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_population')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_build_time')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_hourly_production')->end()
                    ->scalarNode('max_storage')->end()
                    ->append($this->addRequiresSection())
                    ->append($this->addCalculatorSection('build_time_calculator', true))
                    ->append($this->addCalculatorSection('cost_calculator'))
                    ->append($this->addCalculatorSection('production_calculator'))
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
            ->end();

        $children
            ->arrayNode('ships')
            ->isRequired()
            ->useAttributeAsKey('type')
            ->fixXmlConfig('ship')
            ->arrayPrototype()
                ->children()
                    ->integerNode('hull')->isRequired()->end()
                    ->scalarNode('base_speed')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_mass')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('base_capacity')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end();
        $children->end();




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

    private function addCalculatorSection(string $nodeName, bool $isRequired = false): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($nodeName);

        $node = $treeBuilder->getRootNode()
            ->children()
                ->stringNode('service')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('parameters')->ignoreExtraKeys()->end()
            ->end();
        if ($isRequired) {
            $node->isRequired();
        }

        return $node;
    }
}