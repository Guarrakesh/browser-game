<?php

use App\Constants;
use Symfony\Config\GameConfig;

return static function (GameConfig $config) {
    $hub = $config->buildings(Constants::CONTROL_HUB);

    $hub->maxLevel(10)
        ->minLevel(1);
    $hub->baseCost()
        ->concrete(8)
        ->metals(5)
        ->circuits(3)
        ->food(2);
    $hub->basePopulation(5)
        ->costFactor(1.27)
        ->requires([]);

    $config->buildings(Constants::CONCRETE_EXTRACTOR)
        ->maxLevel(25)
        ->minLevel(0)
        ->basePopulation(0)
        ->increaseFactor(1.15)
        ->hourlyProduction(20)
        ->costFactor(1.27)
        ->requires([Constants::CONTROL_HUB => 1])
        ->baseCost()
        ->concrete(4)
        ->metals(9)
        ->circuits(5);

    $config->buildings(Constants::METAL_REFINERY)
        ->maxLevel(25)
        ->minLevel(0)
        ->basePopulation(8)
        ->increaseFactor(1.15)
        ->hourlyProduction(20)
        ->requires([Constants::CONTROL_HUB => 1])
        ->costFactor(1.30)
        ->baseCost()
        ->concrete(8)
        ->metals(5)
        ->circuits(4);


    $config->buildings(Constants::CIRCUIT_ASSEMBLY_PLANT)
        ->maxLevel(25)
        ->minLevel(0)
        ->basePopulation(11)
        ->increaseFactor(1.15)
        ->hourlyProduction(20)
        ->requires([Constants::CONTROL_HUB => 1])
        ->costFactor(1.315)
        ->baseCost()
        ->concrete(10)
        ->metals(8)
        ->circuits(5);

    $config->buildings(Constants::HYDROPONIC_FARM)
        ->maxLevel(25)
        ->minLevel(0)
        ->basePopulation(5)
        ->increaseFactor(1.06)
        ->hourlyProduction(20)
        ->requires([Constants::CONTROL_HUB => 1])
        ->costFactor(1.28)
        ->baseCost()
        ->concrete(12)
        ->metals(14)
        ->circuits(4);

    $config->buildings(Constants::STORAGE_BAY)
        ->maxLevel(20)
        ->minLevel(1)
        ->basePopulation(0)
        ->maxStorage(100)
        ->increaseFactor(1.24)
        ->costFactor(1.22)
        ->requires([Constants::CONTROL_HUB => 1]);
    ;
};