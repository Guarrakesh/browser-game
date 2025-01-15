<?php

namespace App\Ship;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class ShipRegistry
{
    public function __construct(
        #[AutowireLocator(ShipDefinitionInterface::class)]
        private ServiceLocator $shipsLocator
    )
    {
    }

    /** @return array<ShipDefinition> */
    public function getShips(): array {
        return iterator_to_array($this->shipsLocator->getIterator());
    }

    public function getShip(string $name): ?ShipDefinition
    {
        return $this->shipsLocator->has($name) ? $this->shipsLocator->get($name) : null;
    }


}