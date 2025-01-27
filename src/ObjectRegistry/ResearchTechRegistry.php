<?php

namespace App\ObjectRegistry;

use App\Exception\GameObjectNotFoundException;
use App\ObjectDefinition\ObjectType;
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
    public function find(string $tech): ?ResearchTechDefinitionInterface
    {
        return $this->techsLocator->has($tech) ? $this->techsLocator->get($tech) : null;
    }

    public function get(string $tech): ResearchTechDefinitionInterface
    {
        if (!$this->techsLocator->has($tech)) {
            throw new GameObjectNotFoundException(ObjectType::ResearchTech, $tech);
        }

        return $this->techsLocator->get($tech);
    }
}