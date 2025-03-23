<?php

namespace App\DependencyInjection\Modules;

use App\Modules\Planet\GameObject\ShipComponent\ShipComponentDefinition;
use App\Modules\Planet\GameObject\ShipComponent\ShipComponentDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ShipComponentModule implements ModuleConfigurationInterface
{

    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('ship_components', 'array');
        $builder->getRootNode()

            ->isRequired()
                ->useAttributeAsKey('name')
                ->fixXmlConfig('ship_component')
                ->arrayPrototype()
                ->children()
                    ->scalarNode('slots')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('mass')->cannotBeEmpty()->defaultValue(0)->end()
                    ->scalarNode('attack')->cannotBeEmpty()->defaultValue(0)->end()
                    ->scalarNode('shield')->cannotBeEmpty()->defaultValue(0)->end()
                ->end()
            ->end();
        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {
        foreach ($config['ship_components'] as $componentName => $componentConfig) {
            $definition = new Definition(ShipComponentDefinition::class);
            $definition
                ->setArgument('$name', $componentName)
                ->setArgument('$config', $componentConfig)
                ->addTag(ShipComponentDefinitionInterface::class, ['key' => $componentName])
                ->setAutoconfigured(true);
            $container->setDefinition('ship_components.'.$componentName, $definition);
        }


    }

    public function processDefaultValues(ContainerBuilder $container): void
    {
        // noop

    }
}