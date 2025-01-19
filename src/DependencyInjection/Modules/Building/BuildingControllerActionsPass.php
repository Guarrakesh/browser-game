<?php

declare(strict_types=1);

namespace App\DependencyInjection\Modules\Building;

use App\Controller\World\Building\BuildingControllerInterface;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Injects a BuildingController with its actions
 */
class BuildingControllerActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $buildings = [];
        foreach ($container->findTaggedServiceIds(BuildingDefinitionInterface::class) as $id => $tags) {
            if (isset($tags[0]['key'])) {
                $buildings[] = $tags[0]['key'];
            }
        }

        foreach ($container->findTaggedServiceIds(BuildingControllerInterface::class) as $id => $tags) {
            if (!is_a($id, BuildingControllerInterface::class, true)) {
                continue;
            }

            $buildingName = $id::getType();
            if (!in_array($buildingName, $buildings) || !$container->hasDefinition($id)) {
                continue;
            }

            $def = $container->getDefinition($id);
            try {
                $reflClass = new ReflectionClass($id);
                $constrParams = $reflClass->getConstructor()->getParameters();
                foreach ($constrParams as $param) {
                    if ($param->getName() === 'actions' && $param->getType()?->getName() === ServiceLocator::class) {
                        $def->replaceArgument('$actions', new ServiceLocatorArgument(
                            new TaggedIteratorArgument($buildingName . '.actions', defaultIndexMethod: 'getName')
                        ));
                        break;
                    }
                }

            } catch (OutOfBoundsException|ReflectionException) {
            }
        }

    }
}
