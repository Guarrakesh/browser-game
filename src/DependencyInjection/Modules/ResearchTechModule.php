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

    static string $invalidTechName = "";
    static string $invalidCalculator = "";
    public function addConfig(NodeBuilder $node): void
    {
        $builder = new TreeBuilder('techs', 'array');
        $builder->getRootNode()

            ->children()
                ->append($this->getCalculatorSection('default_research_time_calculator'))
                ->append($this->getCalculatorSection('default_cost_calculator'))
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
                        ->end()

                    ->end()

                ->end()
            ->end()
            ->validate()
                ->ifTrue(fn ($v) => self::invalidCostCalculator($v))
                ->then(fn ($v) => throw new \InvalidArgumentException(sprintf("Tech '%1\$s' doesn't have a %2\$s. Add a '%1\$s.%2\$s' configuration or set a 'techs.default_%2\$s'", ResearchTechModule::$invalidTechName, ResearchTechModule::$invalidCalculator)))
            ->end();



        $node->append($builder->getRootNode());
    }

    public function processConfiguration(array $config, ContainerBuilder $container): void
    {
        foreach ($config['techs'] as $techName => $techConfig) {
            $definition = new Definition(ResearchTechDefinition::class);
            $definition
                ->setArgument('$name', $techName)
                ->setArgument('$config', $techConfig)
                ->addTag(ResearchTechDefinitionInterface::class, ['key' => $techName])
                ->setAutoconfigured(true);
            $container->setDefinition('tech.'.$techName, $definition);
        }
    }

    public function processDefaultValues(ContainerBuilder $container): void
    {

    }

    public static function invalidCostCalculator(array $config): bool
    {
        $defaultCost = $config['default_cost_calculator'] ?? null;
        $defaultResearchTime = $config['default_research_time_calculator'] ?? null;

        if ($defaultCost && $defaultResearchTime) {
            return false;
        }

        foreach ($config['list'] as $techName => $tech ) {
            if (!isset($tech['cost_calculator'])) {
                ResearchTechModule::$invalidTechName = $techName;
                ResearchTechModule::$invalidCalculator = 'cost_calculator';
                return true;
            }

            if (!isset($tech['research_time_calculator'])) {
                ResearchTechModule::$invalidTechName = $techName;
                ResearchTechModule::$invalidCalculator = 'research_time_calculator';
                return true;
            }
        }

        return false;
    }
}