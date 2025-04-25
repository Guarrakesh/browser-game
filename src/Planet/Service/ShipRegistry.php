<?php

namespace App\Planet\Service;

use App\Planet\GameObject\Ship\ShipDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class
ShipRegistry
{
    public function __construct(
        #[AutowireLocator(ShipDefinitionInterface::class)]
        private ServiceLocator $shipsLocator
    )
    {
    }

    /** @return array<ShipDefinitionInterface> */
    public function getShips(): array {
        return iterator_to_array($this->shipsLocator->getIterator());
    }

    public function getShip(string $name): ?ShipDefinitionInterface
    {
        return $this->shipsLocator->has($name) ? $this->shipsLocator->get($name) : null;
    }


}