<?php

namespace App\Modules\Construction\Controller;

use App\ObjectRegistry\BuildingRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class AbstractBuildingAction implements BuildingActionInterface, ServiceSubscriberInterface
{
    public function __construct(protected readonly ContainerInterface $container)
    {
    }

    public static function getSubscribedServices(): array
    {
        return AbstractController::getSubscribedServices() + [
                BuildingRegistry::class,

            ];
    }


}