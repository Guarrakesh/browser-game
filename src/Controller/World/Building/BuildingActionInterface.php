<?php

namespace App\Controller\World\Building;


use App\Entity\World\CampBuilding;
use App\Model\ViewModel\BaseViewModel;
use App\Model\ViewModel\BuildingViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BuildingActionInterface
{
    public function execute(Request $request, CampBuilding $building): BuildingViewModel;

    public static function getName(): string;

}