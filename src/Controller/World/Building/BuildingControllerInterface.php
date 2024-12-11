<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface BuildingControllerInterface
{
    public static function getType(): string;

    public function handle(Request $request, CampBuilding $building): Response;
}