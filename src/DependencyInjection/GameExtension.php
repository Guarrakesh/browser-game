<?php

namespace App\DependencyInjection;

use App\Service\Camp\Building\BuildingConfigProvider;
use App\Service\Camp\Building\BuildingConfigProviderInterface;
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
        $processor->processConfiguration($configuration, $configs);

        $config = $configs[0];
        foreach ($config['buildings'] as $buildingName => $buildingConfig) {
            $definition = new Definition(BuildingConfigProvider::class);
            $definition
                ->setArgument('$config', $buildingConfig)
                ->setArgument('$name', $buildingName)
                ->setAutowired(true)
                ->addTag(BuildingConfigProviderInterface::class, ['key' => $buildingName])
                ->setAutoconfigured(true);

            $container->setDefinition('building.provider.' . $buildingName, $definition);
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