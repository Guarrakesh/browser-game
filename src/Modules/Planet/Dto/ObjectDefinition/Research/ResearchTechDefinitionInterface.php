<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\Research;

use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Shared\Model\ResourcePack;

interface ResearchTechDefinitionInterface extends BaseDefinitionInterface
{
    /** @return array{'buildings': array<string,int>, 'techs': array<string,int>} */
    public function getRequires(): array;

    public function getLabel(): string;

    public function getDescription(): string;

    public function getBaseCost(): ResourcePack;



}