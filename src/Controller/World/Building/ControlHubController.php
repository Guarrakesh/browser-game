<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlHubController extends AbstractController implements BuildingControllerInterface
{

    public static function getType(): string
    {
        return 'control_hub';
    }

    public function handle(Request $request, CampBuilding $building): Response
    {
        return $this->render('camp/buildings/control_hub/index.html.twig', [
            'building' => $building,
            'camp' => $building->getCamp(),
        ]);
    }
}