<?php

namespace App\Tests\Modules\Planet\Model\Entity;

use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Model\ResourcePack;
use PHPUnit\Framework\TestCase;

class PlanetConstructionsTest extends TestCase
{
    public function testAddResourcesMaxesOutMaxStorage()
    {
        $planet = new Planet('my_planet', 100);
        $planet->creditResources(ResourcePack::fromIdentity(2000));

        $this->assertEquals(1000, $planet->getStorageAsPack()->getConcrete());
        $this->assertEquals(1000, $planet->getStorageAsPack()->getCircuits());
        $this->assertEquals(1000, $planet->getStorageAsPack()->getMetals());
        $this->assertEquals(1000, $planet->getStorageAsPack()->getFood());
    }

    public function testUpgradeBuildingIncreasesLevel()
    {
        $planet = new Planet('my_planet');
        $building = new BuildingDefinition('test_building', ['min_level' => 1, 'max_level' => 10]);

        $planet->upgradeBuilding($building, 1);
        $this->assertEquals(1, $planet->getBuildingLevel('test_building'));
        $planet->upgradeBuilding($building, 5);
        $this->assertEquals(5, $planet->getBuildingLevel('test_building'));

    }

    public function testUpgradeBuildingMaxesOutLevel(): void
    {
        $planet = new Planet('my_planet');
        $building = $this->getBasicBuildingDefinition('test_building');

        $this->assertEquals(0, $planet->getBuildingLevel('test_building'));
        $planet->upgradeBuilding($building, 25);
        $this->assertEquals(20, $planet->getBuildingLevel('test_building'));
    }

    public function testDowngradedBuildingBelowMinRemovesBuilding(): void
    {
        $planet = new Planet('my_planet');
        $building = $this->getBasicBuildingDefinition('test_building');

        $planet->upgradeBuilding($building, 4);
        $this->assertEquals(4, $planet->getBuildingLevel('test_building'));
        $this->assertTrue($planet->hasBuilding('test_building'));

        $planet->upgradeBuilding($building, -5);
        $this->assertFalse($planet->hasBuilding('test_building'));
        $this->assertEquals(0, $planet->getBuildingLevel('test_building'));
    }

    public function testUpgradingStorageBayIncreasesMaxStorage(): void
    {
        $planet = new Planet('my_planet');
        $storageBay = new BuildingDefinition('storage_bay', [
            'max_level' => 20,
            'min_level' => 1,
            'parameters' => [
                'storage_increase_factor' => 2,
                'base_storage' => 1000,
            ],
        ]);


        $planet->upgradeBuilding($storageBay, 2);
        $this->assertEquals(2000, $planet->getMaxStorage());
        $planet->upgradeBuilding($storageBay, 3);
        $this->assertEquals(4000, $planet->getMaxStorage());
    }

    public function testCanBeBuiltReturnsFalseOnUnSatisfiedRequirements(): void
    {
        $definition = new BuildingDefinition('test_building', [
            'min_level' => 1,
            'max_level' => 20,
            'requires' => [
                'buildings' => ['base_building' => 1, 'base_building_2' => 4]
            ]
        ]);
        $baseBuildingDef = $this->getBasicBuildingDefinition('base_building');
        $baseBuilding2Def = $this->getBasicBuildingDefinition('base_building_2');
        $planet = new Planet('my_planet');
        $planet->upgradeBuilding($baseBuildingDef, 1);
        $planet->upgradeBuilding($baseBuilding2Def, 2);

        $this->assertFalse($planet->areBuildingRequirementsMet($definition));

    }

    public function testCanBeBuildReturnsTrueOnSatisfiedRequirements(): void
    {
        $definition = new BuildingDefinition('test_building', [
            'requires' => [
                'buildings' => ['base_building' => 1, 'base_building_2' => 2]
            ]
        ]);
        $baseBuildingDef = $this->getBasicBuildingDefinition('base_building');
        $baseBuilding2Def = $this->getBasicBuildingDefinition('base_building_2');
        $planet = new Planet('my_planet');
        $planet->upgradeBuilding($baseBuildingDef, 1);
        $planet->upgradeBuilding($baseBuilding2Def, 2);

        $this->assertTrue($planet->areBuildingRequirementsMet($definition));
    }

    private function getBasicBuildingDefinition(string $name): BuildingDefinition
    {
        return new BuildingDefinition($name, [
            'min_level' => 1,
            'max_level' => 20,
        ]);
    }

}
