<?php

namespace App\Service\ObjectTime;

use App\Constants;
use App\Event\ObjectTimeEvent;
use App\Helper\MemoizerTrait;
use App\Modules\Core\Entity\Planet;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\ObjectDefinition\ObjectType;
use App\Service\Cost\CostService;
use App\Service\UniverseSettingsService;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class ObjectTimeService
{
    use MemoizerTrait;

    /**
     * @param iterable<TimeEffectInterface> $timeEffects
     */
    public function __construct(
        private readonly CostService                                              $costService,
        private readonly UniverseSettingsService                                  $universeSettingsService,
        #[AutowireIterator(TimeEffectInterface::class)] private readonly iterable $timeEffects
    )
    {
    }

    public function getTimeForObject(Planet $planet, BaseDefinitionInterface $definition, ?int $level): int
    {


        $key = spl_object_hash($planet) . '$' . spl_object_hash($definition) . '$' . $level;

        return $this->memoize($key, function () use ($planet, $definition, $level) {
            $cost = $this->costService->getCostForObject($planet, $definition, $level);
            $speed = $this->universeSettingsService->getUniverseSpeed();
            $time = 0;
            if ($definition->getType() === ObjectType::Building) {
                $controlHubLevel = $planet->getBuilding(Constants::CONTROL_HUB)->getLevel();
                $time = $cost->total() / 5 *  ((1.04 ** $controlHubLevel) * $speed);

            } elseif ($definition->getType() === ObjectType::ResearchTech) {
                $researchCenterLevel = $planet->getBuilding(Constants::RESEARCH_CENTER)->getLevel();
                $time = $cost->total () / ((1.02 ** $researchCenterLevel) * $speed);

            } elseif ($definition->getType() === ObjectType::Ship) {

            } elseif ($definition->getType() === ObjectType::ShipComponent) {

            } else {
                throw new \LogicException(sprintf("Invalid object %s:%s to calculate time", $definition->getType()->name, $definition->getName()));
            }

            foreach ($this->timeEffects as $effect) {
                $time = $effect->processTime($planet, $definition, $level, $time);
            }

            return $time;
        });


    }


}