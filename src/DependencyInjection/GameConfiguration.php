<?php

namespace App\DependencyInjection;

use App\DependencyInjection\Modules\ModuleTrait;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class GameConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('game');

        $rootNode = $treeBuilder->getRootNode();

        $children = $rootNode->children();

        foreach (GameExtension::MODULES as $module) {
            $instance = new $module();
            $instance->addConfig($children);
        }
        $children->end();


        return $treeBuilder;



    }


}