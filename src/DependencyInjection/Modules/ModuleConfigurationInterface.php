<?php

namespace App\DependencyInjection\Modules;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ModuleConfigurationInterface
{
    public function addConfig(NodeBuilder $node): void;
    public function processConfiguration(array $config, ContainerBuilder $container): void;

    public function processDefaultValues(ContainerBuilder $container): void;
}