<?php

namespace App\ObjectDefinition\Research;

use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;

interface ResearchTechDefinitionInterface extends BaseDefinitionInterface
{
    /** @return string[] */
    public function getRequires(): array;

    public function getLabel(): string;

    public function getDescription(): string;

    public function getBaseCost(): ResourcePack;

}