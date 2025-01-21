<?php

namespace App\ObjectDefinition\Research;

use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\ObjectDefinition\DefinitionWithCalculatorInterface;

interface ResearchTechDefinitionInterface extends BaseDefinitionInterface, DefinitionWithCalculatorInterface
{
    /** @return string[] */
    public function getRequires(): array;

    public function getLabel(): string;

    public function getDescription(): string;

    public function getBaseCost(): ResourcePack;

}