<?php

declare(strict_types=1);

namespace App\Configuration\Building;

use App\Service\BuildingConfigurationService;
use App\Service\Camp\Building\BuildingConfigProvider;
use App\Service\Camp\Building\BuildingConfigProviderInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class BuildingConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $configDir = $container->getParameter('kernel.project_dir') . '/config';
        $locator = new FileLocator($configDir);

        $buildingsFile = $locator->locate('buildings.yaml');
        $loader = new BuildingConfigLoader($locator);

        $config = $loader->load($buildingsFile);

        $configuration = new BuildingsConfiguration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, $config);


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
}
