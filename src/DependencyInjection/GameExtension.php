<?php

namespace App\DependencyInjection;

use App\DependencyInjection\Modules\Building\BuildingModule;
use App\DependencyInjection\Modules\DroneModule;
use App\DependencyInjection\Modules\ModuleConfigurationInterface;
use App\DependencyInjection\Modules\ResearchTechModule;
use App\DependencyInjection\Modules\ShipComponentModule;
use App\DependencyInjection\Modules\ShipModule;
use App\DependencyInjection\Modules\TimeModule;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionTrait;

class GameExtension implements ExtensionInterface, ConfigurationExtensionInterface
{
    use ExtensionTrait;

    /** @var array<class-string<ModuleConfigurationInterface>>*/
    public const MODULES = [
        BuildingModule::class,
        ShipModule::class,
        ShipComponentModule::class,
        ResearchTechModule::class,
        DroneModule::class,
        TimeModule::class,
    ];
    public function load(array $configs, ContainerBuilder $container): void
    {

        $configuration = $this->getConfiguration($configs, $container);
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);


        foreach (self::MODULES as $module) {
            $instance = new $module();
            $instance->processDefaultValues($container);
            $instance->processConfiguration($config, $container);
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