<?php

namespace App\DependencyInjection\Modules;

use App\ObjectDefinition\Research\ResearchTechDefinition;
use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResearchTechModule implements ModuleConfigurationInterface
{
    use ModuleTrait;
    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('techs', 'array');
        $builder->getRootNode()

            ->children()
                ->append($this->getParametersSection())
                ->arrayNode('list')
                    ->isRequired()
                    ->useAttributeAsKey('name')
                    ->fixXmlConfig('tech')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('description')->isRequired()->cannotBeEmpty()->end()
                            ->arrayNode('requires')
                                    ->scalarPrototype()->end()
                            ->end()
                            ->append($this->getBaseCostDefinition('base_cost', false))
                            ->arrayNode('unlocks')
                                ->scalarPrototype()->end()
                            ->end()
                            ->append($this->getParametersSection())
                        ->end()

                    ->end()

                ->end()
            ->end()
            ;
        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {
        $defaultParams = $config['techs']['parameters'] ?? [];
        foreach ($config['techs']['list'] as $techName => $techConfig) {
            $definition = new Definition(ResearchTechDefinition::class);
            $definition
                ->setArgument('$name', $techName)
                ->setArgument('$config', array_merge_recursive($defaultParams, $techConfig))
                ->addTag(ResearchTechDefinitionInterface::class, ['key' => $techName])
                ->setAutoconfigured(true);
            $container->setDefinition('tech.'.$techName, $definition);
        }
    }

    public function processDefaultValues(ContainerBuilder $container): void
    {

    }


}