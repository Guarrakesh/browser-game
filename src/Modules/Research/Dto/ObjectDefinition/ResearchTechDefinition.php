<?php

namespace App\Modules\Research\Dto\ObjectDefinition;

use App\Modules\Planet\Dto\ObjectDefinition\AbstractDefinition;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ObjectType;

class ResearchTechDefinition extends AbstractDefinition implements ResearchTechDefinitionInterface
{

    /** @var GameObjectLevel[] */
    private ?array $_researchRequirements = null;

    public function getRequires(): array
    {
        return $this->getConfig('requires');
    }

    public function getLabel(): string
    {
        return $this->getConfig('label');
    }

    public function getDescription(): string
    {
       return $this->getConfig('description');
    }


    public function getType(): ObjectType
    {
        return ObjectType::ResearchTech;
    }


    public function getRequirements(): array
    {
        if ($this->_researchRequirements === null) {
            $this->_researchRequirements = [];
            $requires = $this->config['requires'];
            foreach ($requires as $type => $requirements) {
                foreach ($requirements as $objectName => $level) {
                    $this->_researchRequirements[] = new GameObjectLevel(
                        new GameObject($objectName, ObjectType::fromConfigLabel($type)),
                        $level
                    );
                }
            }
        }

        return $this->_researchRequirements;
    }
}