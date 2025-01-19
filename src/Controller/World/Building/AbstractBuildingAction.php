<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use App\ObjectRegistry\BuildingRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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