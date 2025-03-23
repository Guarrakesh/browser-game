<?php

namespace App\DependencyInjection\Modules;

use App\Modules\Planet\GameObject\Ship\ShipDefinition;
use App\Modules\Planet\GameObject\Ship\ShipDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ShipModule implements ModuleConfigurationInterface
{

    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('ships', 'array');
        $builder->getRootNode()

                ->isRequired()
                ->useAttributeAsKey('name')
                ->fixXmlConfig('ship')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('mass')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('hull')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('attack')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('shield')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('speed')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('cargo')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('slots')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end();
        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {

        foreach ($config['ships'] as $shipName => $shipConfig) {
            $definition = new Definition(ShipDefinition::class);
            $definition
                ->setArgument('$name', $shipName)
                ->setArgument('$config', $shipConfig)
                ->addTag(ShipDefinitionInterface::class, ['key' => $shipName])
                ->setAutoconfigured(true);
            $container->setDefinition('ships.' . $shipName, $definition);
        }
    }

    public function processDefaultValues(ContainerBuilder $container): void
    {
        // noop
    }
}