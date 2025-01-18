<?php

namespace App\DependencyInjection\Modules;

use App\ObjectDefinition\Building\BuildingDefinition;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class BuildingModule implements ModuleConfigurationInterface
{

    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('buildings', 'array');
        $builder->getRootNode()
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

        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {

        foreach ($config['buildings'] as $buildingName => $buildingConfig) {
            $definition = new Definition(BuildingDefinition::class);
            $definition
                ->setArgument('$config', $buildingConfig)
                ->setArgument('$name', $buildingName)
                ->addTag(BuildingDefinitionInterface::class, ['key' => $buildingName])
                ->setAutoconfigured(true);

            $container->setDefinition('building.provider.' . $buildingName, $definition);
        }

    }

    public function processDefaultValues(ContainerBuilder $container): void
    {

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
            ->arrayNode('parameters')
            ->floatPrototype()->end()
            ->end()
            ->end();
        if ($isRequired) {
            $node->isRequired();
        }

        return $node;
    }
}