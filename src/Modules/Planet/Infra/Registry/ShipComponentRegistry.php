<?php

namespace App\Modules\Planet\Infra\Registry;

use App\Modules\Planet\Dto\ObjectDefinition\ShipComponent\ShipComponentDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ShipComponentRegistry
{
    public function __construct(
        #[AutowireLocator(ShipComponentDefinitionInterface::class)]
        private ServiceLocator $shipComponentLocator
    ) {}

    public function getComponents(): array {
        return iterator_to_array($this->shipComponentLocator->getIterator());
    }

    public function getShipComponent(string $name): ?ShipComponentDefinitionInterface
    {
        return $this->shipComponentLocator->has($name) ? $this->shipComponentLocator->get($name) : null;
    }
}