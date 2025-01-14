<?php

namespace App\Controller\World\Building;


use App\Entity\World\CampBuilding;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BuildingActionInterface
{
    public function execute(Request $request, CampBuilding $building): ?Response;

}