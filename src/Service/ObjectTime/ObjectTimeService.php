<?php

namespace App\Service\ObjectTime;

use App\Entity\World\Camp;
use App\Event\ObjectTimeEvent;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\Service\UniverseSettingsService;
use Psr\EventDispatcher\EventDispatcherInterface;

class ObjectTimeService
{
    public function __construct(private readonly UniverseSettingsService $universeSettingsService, private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function getTimeForObject(Camp $camp, BaseDefinitionInterface $definition, ?int $level): int
    {
        $total = $definition->getBaseCost()->total();

        $time = 4000 / $this->universeSettingsService->getUniverseSpeed();
        $event = new ObjectTimeEvent($camp, $definition, $level, $time);
        $this->eventDispatcher->dispatch($event);

        return $event->getTime();
    }

}