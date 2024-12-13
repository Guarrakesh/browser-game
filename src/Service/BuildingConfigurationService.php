<?php

namespace App\Service;

use App\Entity\World\Camp;
use App\Model\Building\CampBuildingList;
use App\Service\Camp\Building\BuildingConfigProvider;
use App\Service\Camp\Building\BuildingConfigProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class BuildingConfigurationService
{
    /**
     * @param ServiceLocator<BuildingConfigProviderInterface> $buildingConfigs
     */
    public function __construct(
        #[AutowireLocator(BuildingConfigProviderInterface::class, indexAttribute: 'key')] private readonly ServiceLocator $buildingConfigs
    )
    {}

    public function getBuildingConfigProvider(string $name): BuildingConfigProvider
    {
        return $this->buildingConfigs->get($name);
    }

    public function getStartupBuildingConfig(): CampBuildingList
    {
        $buildingList = new CampBuildingList();

        foreach ($this->buildingConfigs->getIterator() as $provider) {
            /** @var BuildingConfigProviderInterface $provider */
            //if ($provider->getRequirements()->isSatisfied())
            if ($provider->getMinLevel() > 0) {
                $buildingList->addBuilding($provider->getName(), $provider->getMinLevel());
            }
        }

        return $buildingList;
    }


}