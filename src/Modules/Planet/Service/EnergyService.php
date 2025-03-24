<?php

namespace App\Modules\Planet\Service;

use App\Modules\Planet\Dto\MemoizerTrait;
use App\Modules\Planet\Event\EnergyYieldIntegrationEvent;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Constants;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EnergyService
{
    use MemoizerTrait;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        // TODO: Make this a configuration
        private readonly array $energyBuildings = [Constants::NUCLEAR_REACTOR, Constants::SOLAR_PANEL]
    )
    {
    }

    public function getEnergyYield(Planet $planet): int
    {
        $key = $planet->getId() . $planet->getUpdatedAt()->getTimestamp() . '__energyYield';

        return $this->memoize(
            $key,
            function () use ($planet) {
                $power = $planet->getEnergyYield($this->energyBuildings);

                $event = $this->dispatcher->dispatch(new EnergyYieldIntegrationEvent($power, $planet->getId()));

                return round($event->getEnergy());
            });


    }

    public function getEnergyConsumption(Planet $planet): float
    {
        $key = $planet->getId() . $planet->getUpdatedAt()->getTimestamp() . '__energyConsumption';
        return $this->memoize(
            $key,
            function () use ($planet) {
                $power = $planet->getEnergyConsumption();

                $event = $this->dispatcher->dispatch(new EnergyYieldIntegrationEvent($power, $planet->getId()));

                return $event->getEnergy();
            }
        );
    }

    public function getAvailableEnergy(Planet $planet): float
    {
        return $this->getEnergyYield($planet) - $this->getEnergyConsumption($planet);
    }

    /**
     * @return bool True if the current planet energy consumption added with the given
     * energy is less or equal the total planet's yield. False otherwise
     */
    public function canYieldEnergy(float $energy, Planet $planet): bool
    {
        $consumption = $this->getEnergyConsumption($planet);
        $yield = $this->getEnergyYield($planet);

        return $consumption + $energy <= $yield;
    }

}