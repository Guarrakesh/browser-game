<?php

namespace App\Modules\Planet\Model\DomainService\ObjectTime;

use App\Helper\MemoizerTrait;
use App\Modules\Core\Infra\Repository\UniverseSettingsRepository;
use App\Modules\Planet\Dto\ObjectDefinition\BaseDefinitionInterface;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\Entity\Planet;
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

    public function getTimeForObject(Planet $planet, BaseDefinitionInterface $definition, ?int $level, ResourcePack $cost): int
    {

        $key = spl_object_hash($planet) . '$' . spl_object_hash($definition) . '$' . $level;

        return $this->memoize($key, function () use ($planet, $definition, $level, $cost) {
            $speed = $this->universeSettingsService->getUniverseSpeed();
            $time = 0;
            if ($definition->getType() === ObjectType::Building) {
                $controlHubLevel = $planet->getBuildingLevel(Constants::CONTROL_HUB);
                $time = $cost->total() / 5 *  ((1.04 ** $controlHubLevel) * $speed);

            } elseif ($definition->getType() === ObjectType::ResearchTech) {
                $researchCenterLevel = $planet->getBuildingLevel(Constants::RESEARCH_CENTER);
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