<?php

namespace App\Planet\Twig;


use App\Planet\Dto\ControlHubDTO;
use App\Planet\Service\ControlHubService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class BuildingList
{
    use DefaultActionTrait;

    #[LiveProp(useSerializerForHydration: true)]
    public ControlHubDTO $controlHub;

    #[LiveProp]
    public bool $queued = false;

    public function __construct(private ControlHubService $controlHubService)
    {
    }

    #[LiveAction]
    public function enqueue(int $planetId, #[LiveArg] string $building): void
    {
        $controlHub = $this->controlHubService->enqueueConstruction($planetId, $building);

        $this->controlHub = $controlHub;

    }
}