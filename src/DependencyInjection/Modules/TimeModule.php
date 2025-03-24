<?php

namespace App\DependencyInjection\Modules;

use App\Modules\Shared\ObjectTime\TimeService;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TimeModule implements ModuleConfigurationInterface
{

    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('time', 'array');
        $builder->getRootNode()
            ->isRequired()
            ->children()
            ->floatNode('default_universe_speed')->isRequired()->end()
            ->arrayNode('construction_build_time')
            ->children()
            ->floatNode('control_hub_level_base')->isRequired()->end()
            ->integerNode('denominator_multiplier')->isRequired()->end()
            ->end()
            ->end()
            ->arrayNode('tech_research_time')
            ->children()
            ->floatNode('research_center_level_base')->isRequired()->end()
            ->integerNode('denominator_multiplier')->isRequired()->end()
            ->end()
            ->end()
            ->arrayNode('drone_build_time')
            ->children()
            ->floatNode('control_hub_level_base')->isRequired()->end()
            ->integerNode('denominator_multiplier')->isRequired()->end()
            ->end()
            ->end()
            ->end()
            ->end();

        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {
        // Extract "time" configuration
        $timeConfig = $config['time'];

        $definition = $container->register(TimeService::class);
        $definition->setAutowired(true)
            ->setArgument('$timeConfig', $timeConfig);

        $container->setDefinition(TimeService::class, $definition);
        $container->setAlias('game.object_time_service', TimeService::class);
    }

    public function processDefaultValues(ContainerBuilder $container): void
    {
        // TODO: Implement processDefaultValues() method.
    }
}