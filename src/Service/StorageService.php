<?php

namespace App\Service;

use App\Helper\TransactionTrait;
use App\Planet\Domain\Entity\Planet;
use App\Planet\Service\BuildingRegistry;
use App\Shared\Model\ResourcePack;
use Doctrine\Persistence\ManagerRegistry;

class StorageService
{
    use TransactionTrait;

    public function __construct(
        private readonly BuildingRegistry $buildingConfigurationService, private readonly ManagerRegistry $managerRegistry,
    )
    {
    }


    public function addResources(Planet $planet, ResourcePack $pack): void
    {
        $planet->creditResources($pack);
    }
}