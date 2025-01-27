<?php

namespace App;

use App\DependencyInjection\GameExtension;
use App\DependencyInjection\Modules\Building\BuildingControllerActionsPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new GameExtension());


    }


}
