<?php

namespace App\DependencyInjection\Modules\Building;

use App\DependencyInjection\Modules\ModuleConfigurationInterface;
use App\DependencyInjection\Modules\ModuleTrait;
use App\Modules\Planet\GameObject\Building\BuildingDefinition;
use App\Modules\Planet\GameObject\Building\BuildingDefinitionInterface;
use App\Modules\Planet\GameObject\Building\MineBuildingDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

enum BuildingType: string {
    case Production = 'production';
    case Power = 'power';
    case Other = 'other';

}
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
                            ->enumNode('type')->isRequired()->cannotBeEmpty()->values([BuildingType::Other->value, BuildingType::Power->value, BuildingType::Production->value])->end()
                            ->scalarNode('max_level')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('min_level')->isRequired()->cannotBeEmpty()->end()
                           // ->scalarNode('base_population')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('base_build_time')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('base_hourly_production')->end()
                            ->scalarNode('energy_base_consumption')->isRequired()->end()
                            ->arrayNode('drone_slots')
                                ->children()
                                    ->scalarNode('base')->cannotBeEmpty()->end()
                                    ->floatNode('prod_multiplier')->end()
                                    ->scalarNode('per_level')->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                            ->floatNode('production_increase_factor')->end()
                            ->floatNode('energy_consumption_increase_factor')->end()
                            ->append($this->getRequiresSection())
                            ->append($this->getParametersSection())
                           // ->append($this->getCalculatorSection('build_time_calculator', true))
                           // ->append($this->getCalculatorSection('cost_calculator'))
                           // ->append($this->getCalculatorSection('production_calculator'))
                            ->append($this->getCostDefinition('base_cost', true))
                        ->end()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return $v['type'] === 'production' && (
                                        empty($v['production_increase_factor']) ||
                                        empty($v['drone_slots']['base']) ||
                                        empty($v['drone_slots']['per_level']) ||
                                        empty($v['drone_slots']['prod_multiplier'])
                                    );
                            })
                            ->thenInvalid('production_increase_factor and drone_slots (base, per_level and prod_multiplier) are required when type is "production".')

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
            $className = match($buildingConfig['type']) {
                BuildingType::Production->value => MineBuildingDefinition::class,
                default => BuildingDefinition::class,
            };
            $definition = new Definition($className);
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