<?php

declare(strict_types=1);

namespace App\Configuration\Building;

use App\Security\BuildingConfigurationService;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuildingConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $configDir = $container->getParameter('kernel.project_dir') . '/config';
        $locator = new FileLocator($configDir);

        $buildingsFile =$locator->locate('buildings.yaml');
        $loader = new BuildingConfigLoader($locator);

        $config = $loader->load($buildingsFile);

        $configuration = new BuildingsConfiguration();
        $processor = new Processor();
        $processor->processConfiguration($configuration, $config);

        $container->getDefinition(BuildingConfigurationService::class)
            ->setArgument('$config', $config);
    }
}
