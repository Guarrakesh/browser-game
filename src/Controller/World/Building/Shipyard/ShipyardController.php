<?php

namespace App\Controller\World\Building\Shipyard;

use App\Constants;
use App\Controller\World\Building\BuildingControllerInterface;
use App\Entity\World\CampBuilding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShipyardController extends AbstractController implements BuildingControllerInterface
{

    public static function getType(): string
    {
        return Constants::SHIPYARD;
    }

    public function handle(Request $request, CampBuilding $building): Response
    {
        return $this->render('camp/buildings/shipyard/index.html.twig', [
            'camp' => $building->getCamp(),
            'building' => $building
        ]);
    }
}