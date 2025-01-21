<?php

namespace App\Service;

use App\Camp\StorageService;
use App\CurveCalculator\CurveCalculatorProvider;
use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Entity\World\Queue\CampConstruction;
use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\QueueJob;
use App\Entity\World\Queue\ResearchQueueJob;
use App\Exception\GameException;
use App\Exception\InsufficientResourcesException;
use App\Helper\DBUtils;
use App\Helper\QueueUtil;
use App\Object\ResourcePack;
use App\ObjectRegistry\ResearchTechRegistry;
use App\Repository\PlayerTechRepository;
use App\Repository\ResearchQueueJobRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

class ResearchService
{
    public function __construct(
        private readonly ResearchQueueJobRepository $researchQueueJobRepository, private readonly ResearchTechRegistry $researchTechRegistry, private readonly CurveCalculatorProvider $curveCalculatorProvider, private readonly ManagerRegistry $managerRegistry, private readonly StorageService $storageService, private readonly PlayerTechRepository $playerTechRepository,
    )
    {
    }

    /** @var array<int,Queue<ResearchQueueJob>> */
    private array $playerResearchQueues = [];

    public function enqueueResearch(Camp $camp, string $techName): void
    {
        $queue = $this->getResearchQueue($camp->getPlayer());
        $manager = $this->managerRegistry->getManager('world');
        $player = $camp->getPlayer();
        $storage = $camp->getStorage();

        $callback = function () use ($manager, $player, $storage, $techName, $camp, $queue) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);

            $cost = $this->getCostForResearch($player, $techName);
            $buildTime = $this->getResearchTime($camp, $player, $techName, $cost);

            if (!$this->canBeResearched($camp, $player, $techName, null, $cost)) {
                throw new InsufficientResourcesException($cost);
            }

            $this->storageService->addResources($camp, $cost->multiply(-1));

            $researchJob = new ResearchQueueJob();

            $researchJob->setCamp($camp);
            $researchJob->setPlayer($player);
            $researchJob->setTechName($techName);
            $researchJob->setResourcesUsed($cost);

            $researchJob->setLevel($this->getNextLevelForResearch($player, $techName));
            $queue->enqueueJob($researchJob, $buildTime);

            $manager->persist($researchJob);
            $this->persistQueue($player);
        };


        try {
            DBUtils::transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Throwable $exception) {
            throw new GameException(sprintf("Could not enqueue the construction: %s. Try again.", $exception->getMessage()), 0, $exception);
        }


    }

    public function getResearchQueue(Player $player)
    {

        if (!isset($this->playerResearchQueues[$player->getId()])) {
            $this->playerResearchQueues[$player->getId()] = $this->researchQueueJobRepository->getResearchQueue($player);

        }

        return $this->playerResearchQueues[$player->getId()];
    }

    public function getCostForResearch(Player $player, string $techName, ?int $level = null): ResourcePack
    {
        $researchDefinition = $this->researchTechRegistry->get($techName);
        $level ??= $this->getNextLevelForResearch($player, $techName);
        $calcConfig = $researchDefinition->getCalculatorConfig('cost_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        return $researchDefinition->getBaseCost()->map(
            fn($baseCost) => $calculator->calculateForLevel($level, $baseCost, $calcConfig->parameters)
        );

        // TODO: don't like to dispatch an event in a get() method. Smells of Side effect. Find another way to compose cost calculation, maybe through a dedicated service.

    }

    public function getNextLevelForResearch(Player $player, string $techName): int
    {
        $queue = $this->getResearchQueue($player);

        /** @var Queue<ResearchQueueJob> $existingResearchJobs */
        $existingResearchJobs = QueueUtil::filter($queue, fn(ResearchQueueJob $job) => $job->getTechName() == $techName);
        if (!empty($existingResearchJobs)) {
            return $existingResearchJobs[count($existingResearchJobs) - 1]->getLevel() + 1;
        }

        return ($this->playerTechRepository->findByPlayer($player)?->getLevel($techName) ?? 0) + 1;
    }

    public function getResearchTime(Camp $camp, Player $player, string $techName, ResourcePack $cost): int
    {
        $level ??= $this->getNextLevelForResearch($player, $techName);
        $buildingConfig = $this->researchTechRegistry->get($techName);
        $calcConfig = $buildingConfig->getCalculatorConfig('research_time_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        $universeSpeed = 1.0; // todo: remove hardcode. Move to a Setting Service
        $calcConfig->parameters[] = $cost;
        return $calculator->calculateForLevel($level, $universeSpeed, $calcConfig->parameters);

    }

    public function canBeResearched(Camp $camp, Player $player, string $techName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        // Todo: do requirements check

        return true;
    }

    private function persistQueue(Player $player): void
    {
        if (!isset($this->playerResearchQueues[$player->getId()])) {
            return;
        }

        $manager = $this->managerRegistry->getManager('world');

        QueueUtil::forEach(
            $this->playerResearchQueues[$player->getId()],
            fn (QueueJob $job) => $manager->persist($job));

    }


}