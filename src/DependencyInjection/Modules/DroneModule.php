<?php

namespace App\DependencyInjection\Modules;

use App\Modules\Planet\Service\DroneConfigurationService;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DroneModule implements ModuleConfigurationInterface
{
    use ModuleTrait;
    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('drones', 'array');
        $builder->getRootNode()
            ->isRequired()

            ->children()
            ->append($this->getCostDefinition('base_cost', true))
            ->scalarNode('energy')->cannotBeEmpty()->isRequired()->end()
            ->floatNode('cost_factor')->isRequired()->end()
            ->integerNode('max_planet_drones')->isRequired()->end()
;
        $node->append($builder->getRootNode());


    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {
        $droneConfigurationDef = new Definition(DroneConfigurationService::class);
        $droneConfigurationDef->setAutowired(false);
        $droneConfigurationDef->setArgument('$config', $config['drones']);

        $container->setDefinition('drone_configuration_service', $droneConfigurationDef);

    }

    public function processDefaultValues(ContainerBuilder $container): void
    {

    }
}