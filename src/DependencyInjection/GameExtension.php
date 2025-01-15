<?php

namespace App\DependencyInjection;

use App\Camp\Building\BuildingDefinition;
use App\Camp\Building\BuildingDefinitionInterface;
use App\Ship\ShipDefinition;
use App\Ship\ShipDefinitionInterface;
use App\Ship\ShipRegistry;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionTrait;

class GameExtension implements ExtensionInterface, ConfigurationExtensionInterface
{
    use ExtensionTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);


        foreach ($config['buildings'] as $buildingName => $buildingConfig) {
            $definition = new Definition(BuildingDefinition::class);
            $definition
                ->setArgument('$config', $buildingConfig)
                ->setArgument('$name', $buildingName)
                ->setAutowired(true)
                ->addTag(BuildingDefinitionInterface::class, ['key' => $buildingName])
                ->setAutoconfigured(true);

            $container->setDefinition('building.provider.' . $buildingName, $definition);
        }


        foreach ($config['ships'] as $shipName => $shipConfig) {
            $definition = new Definition(ShipDefinition::class);
            $definition
                ->setArgument('$name', $shipName)
                ->setArgument('$config', $shipConfig)
                ->setAutowired(true)
                ->addTag(ShipDefinitionInterface::class, ['key' => $shipName])
                ->setAutoconfigured(true);
            $container->setDefinition('ships.' . $shipName, $definition);
        }


    }

    public function getNamespace(): string
    {
        return '';
    }

    public function getXsdValidationBasePath(): string|false
    {
        return false;
    }

    public function getAlias(): string
    {
        return 'game';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): GameConfiguration
    {
        return new GameConfiguration();
    }
}