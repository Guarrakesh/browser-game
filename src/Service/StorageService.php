<?php

namespace App\Service;

use App\Constants;
use App\Helper\TransactionTrait;
use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
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
        $planet->getStorage()->addResources($pack, $planet->getMaxStorage());
    }
}