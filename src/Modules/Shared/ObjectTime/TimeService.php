<?php

namespace App\Modules\Shared\ObjectTime;

use App\Modules\Core\Infra\Repository\UniverseSettingsRepository;
use App\Modules\Planet\Dto\MemoizerTrait;
use App\Modules\Planet\Repository\PlanetRepository;
use App\Modules\Shared\Constants;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class TimeService
{
    use MemoizerTrait;

    /**
     * @param iterable<TimeEffectInterface> $timeEffects
     */
    public function __construct(
        private readonly UniverseSettingsRepository                               $universeSettingsService,
        #[AutowireIterator(TimeEffectInterface::class)] private readonly iterable $timeEffects,
        private readonly array $timeConfig,
        private readonly PlanetRepository $planetRepository
    )
    {
    }

    public function getTimeForObject(int $planetId, array $planetBuildings, GameObject $gameObject, ?int $level, ResourcePack $cost): int
    {

        $speed = $this->universeSettingsService->getUniverseSpeed() ?? $this->timeConfig['default_universe_speed'];
        $planet = $this->planetRepository->find($planetId);

        $time = 0;
        if ($gameObject->getType() === ObjectType::Building) {
            if (!array_key_exists(Constants::CONTROL_HUB, $planetBuildings)) {
                return 1;
            }
            $constructionBuildTime = $this->timeConfig['construction_build_time'];

            $base = $constructionBuildTime['control_hub_level_base'];
            $multiplier = $constructionBuildTime['denominator_multiplier'];
            $controlHubLevel = $planetBuildings[Constants::CONTROL_HUB]?->getLevel() ?? 1;

            $time = $cost->total() / ($multiplier * (($base ** $controlHubLevel)) * $speed);

        } elseif ($gameObject->getType() === ObjectType::ResearchTech) {
            $techResearchTime = $this->timeConfig['tech_research_time'];

            $base = $techResearchTime['research_center_level_base'];
            $multiplier = $techResearchTime['denominator_multiplier'];
            $researchCenterLevel = $planetBuildings[Constants::RESEARCH_CENTER]?->getLevel() ?? 1;

            $time = $cost->total() / ($multiplier * (($base ** $researchCenterLevel)) * $speed);

        } elseif ($gameObject->getType() === ObjectType::Drone) {
            if (!array_key_exists(Constants::CONTROL_HUB, $planetBuildings)) {
                return 1;
            }
            $droneBuildTime = $this->timeConfig['drone_build_time'];

            $base = $droneBuildTime['control_hub_level_base'];
            $multiplier = $droneBuildTime['denominator_multiplier'];
            $controlHubLevel = $planetBuildings[Constants::CONTROL_HUB]?->getLevel() ?? 1;

            $time = $cost->total() / ($multiplier * (($base ** $controlHubLevel)) * $speed);
        } elseif ($gameObject->getType() === ObjectType::Ship) {

        } elseif ($gameObject->getType() === ObjectType::ShipComponent) {

        } else {
            throw new \LogicException(sprintf("Invalid object %s:%s to calculate time", $gameObject->getType()->name, $definition->getName()));
        }

       /* foreach ($this->timeEffects as $effect) {
            $time = $effect->processTime($planet, $definition, $level, $time);
        }*/

        return $time;


    }

    public function getUniverseSpeed(): float
    {
        return $this->universeSettingsService->getUniverseSpeed() ?? $this->timeConfig['default_universe_speed'];
    }
}