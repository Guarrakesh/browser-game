<?php

namespace App\DependencyInjection\Modules\Building;

use App\DependencyInjection\Modules\ModuleConfigurationInterface;
use App\DependencyInjection\Modules\ModuleTrait;
use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class BuildingModule implements ModuleConfigurationInterface
{
    use ModuleTrait;

    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('buildings', 'array');
        $builder->getRootNode()
            ->children()
                ->append($this->getParametersSection())
                ->arrayNode('list')
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
                            ->append($this->getRequiresSection())
                            ->append($this->getParametersSection())
                           // ->append($this->getCalculatorSection('build_time_calculator', true))
                           // ->append($this->getCalculatorSection('cost_calculator'))
                           // ->append($this->getCalculatorSection('production_calculator'))
                            ->append($this->getBaseCostDefinition('base_cost', true))
                        ->end()
                    ->end()
                ->end()
            ->end();

        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {

        $defaultParams = $config['buildings']['parameters'] ?? [];
        foreach ($config['buildings']['list'] as $buildingName => $buildingConfig) {
            $definition = new Definition(BuildingDefinition::class);
            $definition
                ->setArgument('$config', array_merge_recursive($defaultParams, $buildingConfig))
                ->setArgument('$name', $buildingName)
                ->addTag(BuildingDefinitionInterface::class, ['key' => $buildingName])
                ->setAutoconfigured(true);

            $container->setDefinition('building.provider.' . $buildingName, $definition);
        }

    }

    public function processDefaultValues(ContainerBuilder $container): void
    {

    }



}