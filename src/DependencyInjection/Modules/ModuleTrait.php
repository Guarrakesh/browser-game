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

    protected function getParametersSection(bool $isRequired = false): NodeDefinition
    {
        $tree = new TreeBuilder('parameters');


        $node = $tree->getRootNode()->scalarPrototype()->end();

        if ($isRequired) {
            $node->isRequired();
        }

        return $tree->getRootNode();
    }

    protected function getRequiresSection(bool $isRequired = false): NodeDefinition
    {
        $tree = new TreeBuilder('requires');
        $tree->getRootNode()
                ->children()
                    ->arrayNode('buildings')
                        ->scalarPrototype()->cannotBeEmpty()->end()
                    ->end()
                    ->arrayNode('techs')
                        ->scalarPrototype()->cannotBeEmpty()->end()
                    ->end()
            ->end();
        if ($isRequired) {
            $tree->getRootNode()->isRequired();
        }
        return $tree->getRootNode();
    }
}