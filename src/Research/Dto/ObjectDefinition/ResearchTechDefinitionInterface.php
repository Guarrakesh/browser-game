<?php

namespace App\Research\Dto\ObjectDefinition;

use App\Shared\GameObject\BaseDefinitionInterface;
use App\Shared\Model\ResourcePack;

interface ResearchTechDefinitionInterface extends BaseDefinitionInterface
{
    /** @return array{'buildings': array<string,int>, 'techs': array<string,int>} */
    public function getRequires(): array;

    public function getLabel(): string;

    public function getDescription(): string;

    public function getBaseCost(): ResourcePack;



}