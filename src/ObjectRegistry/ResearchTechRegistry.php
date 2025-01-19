<?php

namespace App\ObjectRegistry;

use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ResearchTechRegistry
{
    public function __construct(
        #[AutowireLocator(ResearchTechDefinitionInterface::class, indexAttribute: 'key')]
        private ServiceLocator $techsLocator
    )
    {}

    /** @return array<ResearchTechDefinitionInterface> */
    public function getAll(): array {
        return iterator_to_array($this->techsLocator->getIterator());
    }
    public function get(string $tech): ?ResearchTechDefinitionInterface
    {
        return $this->techsLocator->has($tech) ? $this->techsLocator->get($tech) : null;
    }
}