<?php

namespace App\Modules\Research\Service\DomainService;

use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;
use App\Modules\Research\Model\Entity\PlayerTech;
use App\Modules\Shared\Constants;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ObjectType;

class ResearchRequirementsDomainService
{
    /**
     * @param ResearchTechDefinitionInterface $definition
     * @param array<string,PlayerTech> $playerTechs
     * @param array<string,GameObjectLevel> $planetBuildings
     * @return bool
     */
    public function areResearchRequirementsMet(ResearchTechDefinitionInterface $definition, array $playerTechs, array $planetBuildings): bool
    {

        $researchCenterLevel = $planetBuildings[Constants::RESEARCH_CENTER]?->getLevel();
        if (!$researchCenterLevel) {
            // No tech can be researched without a Research Center
            return false;
        }
        foreach ($definition->getRequirements() as $requirement) {
            if ($requirement->getObject()->getType() === ObjectType::Building) {

                $name = $requirement->getObject()->getName();
                $level = $requirement->getLevel();
                if (!array_key_exists($name, $planetBuildings)
                    || $planetBuildings[$name]->getLevel() < $level) {
                    return false;
                }
            } elseif ($requirement->getObject()->getType() === ObjectType::ResearchTech) {
                if (!array_key_exists($requirement->getObject()->getName(), $playerTechs)) {
                    return false;
                }
            }


        }

        return true;
    }


}