<?php

namespace App\ObjectRegistry;

use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;
use App\ObjectDefinition\Research\ResearchTechNode;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ResearchTreeRegistry
{
    /** @var array<ResearchTechNode> */
    private array $nodes = [];
    private array $roots = [];

    public function __construct(
        #[AutowireLocator(ResearchTechDefinitionInterface::class, indexAttribute: 'key')]
        private ServiceLocator $techsLocator
    )
    {
        $this->buildNodes();

    }

    private function buildNodes(): void
    {
        foreach ($this->techsLocator->getIterator() as $key => $definition) {
            $this->nodes[$key] = new ResearchTechNode($definition);
        }
    }

    private function buildTree(): void
    {
        foreach ($this->nodes as $node) {
            if (empty($node->getRequires())) {
                $this->roots[] = $node;
            } else {
                foreach ($node->getRequires() as $techId) {
                    if (isset($this->nodes[$techId])) {
                        $this->nodes[$techId]->addChild($node);
                    }
                }
            }
        }
    }

    public function getUnlockTree(string $technologyId): ?ResearchTechNode
    {
        if (!isset($this->nodes[$technologyId])) {
            return null;
        }

        $tree = $this->nodes[$technologyId];
        $this->buildUnlockSubtree($tree);
        return $tree;
    }


    private function buildUnlockSubtree(ResearchTechNode $node, array &$visited = []): void
    {
        $visited[$node->getDefinition()->getName()] = true;

        foreach ($node->getRequires() as $techId) {
            if (isset($this->nodes[$techId]) && !isset($visited[$techId])) {
                $requireNode = $this->nodes[$techId];
                $node->addChild($requireNode);
                $this->buildUnlockSubtree($requireNode, $visited);
            }
        }
    }


}