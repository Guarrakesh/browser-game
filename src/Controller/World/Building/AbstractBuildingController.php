<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\SubscribedService;


abstract class AbstractBuildingController extends AbstractController implements BuildingControllerInterface
{

    abstract public static function getType(): string;

    abstract public function handle(Request $request, CampBuilding $building): Response;
}