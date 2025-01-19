<?php

namespace App\DependencyInjection\Modules;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

trait ModuleTrait
{
    protected function getBaseCostDefinition(string $name = 'base_cost', bool $isRequired = false): NodeDefinition
    {
        $tree = new TreeBuilder($name, 'array');
        $tree->getRootNode()

            ->children()
                ->scalarNode('concrete')->cannotBeEmpty()->end()
                ->scalarNode('metals')->cannotBeEmpty()->end()
                ->scalarNode('circuits')->cannotBeEmpty()->end()
                ->scalarNode('food')->cannotBeEmpty()->end()
            ->end();

        if ($isRequired) {
            $tree->getRootNode()->isRequired();
        }
        return $tree->getRootNode();

    }

    private function getCalculatorSection(string $nodeName, bool $isRequired = false): NodeDefinition
    {
        $tree = new TreeBuilder($nodeName);

        $tree->getRootNode()
            ->children()
            ->stringNode('service')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('parameters')
            ->floatPrototype()->end()
            ->end()
            ->end();
        if ($isRequired) {
            $tree->getRootNode()->isRequired();
        }

        return $tree->getRootNode();
    }
}