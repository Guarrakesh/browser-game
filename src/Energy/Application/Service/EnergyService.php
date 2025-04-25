<?php

namespace App\Energy\Application\Service;

use App\Energy\Domain\EnergyConsumerInterface;
use App\Planet\Domain\Entity\Planet;
use App\Planet\Dto\MemoizerTrait;
use App\Planet\Service\DroneConfigurationService;
use App\Shared\Constants;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EnergyService
{
    use MemoizerTrait;

    /**
     * @param \IteratorAggregate<EnergyConsumerInterface> $energyConsumers
     */
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        #[Autowire('@drone_configuration_service')] private DroneConfigurationService $droneConfigurationService,
        #[AutowireIterator(EnergyConsumerInterface::class)] private \IteratorAggregate $energyConsumers,
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
                $power = $planet->getBaseEnergyYield($this->energyBuildings);

                //$event = $this->dispatcher->dispatch(new EnergyYieldIntegrationEvent($power, $planet->getId()));

                return round($power);
            });


    }

    // TODO:Make a method to calculated "enqueued" energy based on buildings/drones/units in queue that will make energy consumed increased once finished
    public function getEnergyConsumption(Planet $planet): float
    {

        $key = $planet->getId() . $planet->getUpdatedAt()->getTimestamp() . '__powerPlant';
        return $this->memoize(
            $key,
            function () use ($planet) {
                return array_reduce(
                    iterator_to_array($this->energyConsumers->getIterator()),
                    fn($acc, EnergyConsumerInterface $energyConsumer) => $acc + $energyConsumer->getEnergyConsumption($planet),
                    0
                );
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
        if ($energy <= 0.1) {
            return true;
        }
        $consumption = $this->getEnergyConsumption($planet);
        $yield = $this->getEnergyYield($planet);

        return $consumption + $energy <= $yield;
    }

}