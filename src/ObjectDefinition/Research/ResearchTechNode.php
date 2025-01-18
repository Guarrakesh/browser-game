<?php

namespace App\ObjectDefinition\Research;


class ResearchTechNode
{
    private array $children = [];

    public function __construct(private readonly ResearchTechDefinitionInterface $researchDefinition)
    {}

    public function addChild(ResearchTechNode $child): void
    {
        $this->children[] = $child;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    /** @return string[] */
    public function getRequires(): array
    {
        return $this->researchDefinition->getRequires();
    }

    public function getDefinition(): ResearchTechDefinitionInterface
    {
        return $this->researchDefinition;
    }

}