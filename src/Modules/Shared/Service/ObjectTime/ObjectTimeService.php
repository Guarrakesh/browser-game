<?php

namespace App\Modules\Shared\Service\ObjectTime;

use App\Modules\Core\Infra\Repository\UniverseSettingsRepository;
use App\Modules\Planet\Dto\MemoizerTrait;
use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Shared\Constants;
use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class ObjectTimeService
{
    use MemoizerTrait;

    /**
     * @param iterable<TimeEffectInterface> $timeEffects
     */
    public function __construct(
        private readonly UniverseSettingsRepository                               $universeSettingsService,
        #[AutowireIterator(TimeEffectInterface::class)] private readonly iterable $timeEffects, private readonly PlanetRepository $planetRepository
    )
    {
    }

    public function getTimeForObject(int $planetId, array $planetBuildings, BaseDefinitionInterface $definition, ?int $level, ResourcePack $cost): int
    {

        $speed = $this->universeSettingsService->getUniverseSpeed();
        $time = 0;
        if ($definition->getType() === ObjectType::Building) {
            $controlHubLevel = $planetBuildings[Constants::CONTROL_HUB]?->getLevel() ?? 1;
            $time = $cost->total() / 5 * ((1.04 ** $controlHubLevel) * $speed);

        } elseif ($definition->getType() === ObjectType::ResearchTech) {
            $researchCenterLevel = $planetBuildings[Constants::RESEARCH_CENTER]?->getLevel() ?? 1;
            $time = $cost->total() / ((1.02 ** $researchCenterLevel) * $speed);

        } elseif ($definition->getType() === ObjectType::Ship) {

        } elseif ($definition->getType() === ObjectType::ShipComponent) {

        } else {
            throw new \LogicException(sprintf("Invalid object %s:%s to calculate time", $definition->getType()->name, $definition->getName()));
        }

       /* foreach ($this->timeEffects as $effect) {
            $time = $effect->processTime($planet, $definition, $level, $time);
        }*/

        return $time;


    }


}