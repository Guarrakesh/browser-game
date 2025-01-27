<?php

namespace App\Service;

use App\Entity\World\Player;
use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\QueueJob;
use App\Entity\World\Queue\ResearchQueueJob;
use App\Exception\GameException;
use App\Exception\InsufficientResourcesException;
use App\Helper\QueueUtil;
use App\Helper\TransactionTrait;
use App\Model\BuildingRequirements;
use App\Model\PlanetBuildingList;
use App\Model\TechRequirement;
use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectRegistry\ResearchTechRegistry;
use App\Repository\PlayerTechRepository;
use App\Repository\ResearchQueueJobRepository;
use App\Service\Cost\CostService;
use App\Service\ObjectTime\ObjectTimeService;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Throwable;

class ResearchService
{
    use TransactionTrait;

    public function __construct(
        private readonly ResearchQueueJobRepository $researchQueueJobRepository,
        private readonly ResearchTechRegistry       $researchTechRegistry,
        private readonly CostService                $costService,
        private readonly ManagerRegistry            $managerRegistry,
        private readonly StorageService             $storageService,
        private readonly PlayerTechRepository       $playerTechRepository, private readonly ObjectTimeService $objectTimeService,
    )
    {
    }

    /** @var array<int,Queue<ResearchQueueJob>> */
    private array $playerResearchQueues = [];

    public function enqueueResearch(Planet $planet, string $techName): void
    {
        $queue = $this->getResearchQueue($planet->getPlayer());
        $manager = $this->managerRegistry->getManager('world');
        $player = $planet->getPlayer();
        $storage = $planet->getStorage();

        $callback = function () use ($manager, $player, $storage, $techName, $planet, $queue) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);

            $cost = $this->getCostForResearch($planet, $techName);
            $buildTime = $this->getResearchTime($planet, $player, $techName, $cost);

            if (!$this->canBeResearched($planet, $player, $techName, null, $cost)) {
                throw new InsufficientResourcesException($cost);
            }

            $this->storageService->addResources($planet, $cost->multiply(-1));

            $researchJob = new ResearchQueueJob();

            $researchJob->setPlanet($planet);
            $researchJob->setPlayer($player);
            $researchJob->setTechName($techName);
            $researchJob->setResourcesUsed($cost);

            $researchJob->setLevel($this->getNextLevelForResearch($player, $techName));
            $queue->enqueueJob($researchJob, $buildTime);

            $manager->persist($researchJob);
            $this->persistQueue($player);
        };


        try {
            $this->transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Throwable $exception) {
            throw new GameException(sprintf("Could not enqueue the research: %s. Try again.", $exception->getMessage()), 0, $exception);
        }


    }

    public function cancelConstruction(ResearchQueueJob $job): void
    {
        $manager = $this->managerRegistry->getManager('world');
        $planet =  $job->getPlanet();
        $queue = $this->getResearchQueue($planet->getPlayer());
        $storage = $planet->getStorage();
        $callback = function() use ($manager, $job, $planet, $queue, $storage) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);
            $queue->cancelJob($job);
           // TODO: Research Log ?
            $this->persistQueue($planet->getPlayer());

            $this->storageService->addResources($planet, $job->getResourcesUsed());
        };
        if (!$job->d()) {
            throw new GameException("Research is not persisted.");
        }


        try {
            $this->transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Throwable $exception) {
            throw new GameException(sprintf("Could not cancel the research: %s. Try again.", $exception->getMessage()), 0, $exception);
        }

    }

    /**
     * @throws Exception
     */
    public function getResearchQueue(Player $player, ?Planet $planet = null): Queue
    {
        if (!isset($this->playerResearchQueues[$player->getId()])) {
            $this->playerResearchQueues[$player->getId()] = $this->researchQueueJobRepository->getResearchQueue($player, $planet);

        }

        return $this->playerResearchQueues[$player->getId()];
    }

    public function getCostForResearch(Planet $planet, string $techName, ?int $level = null): ResourcePack
    {
        $researchDefinition = $this->researchTechRegistry->get($techName);
        $level ??= $this->getNextLevelForResearch($planet->getPlayer(), $techName);

        return $this->costService->getCostForObject($planet, $researchDefinition, $level);
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

    public function getResearchTime(Planet $planet, Player $player, string $techName, ResourcePack $cost): int
    {
        $level ??= $this->getNextLevelForResearch($player, $techName);
        $techDefinition = $this->researchTechRegistry->find($techName);

        return $this->objectTimeService->getTimeForObject($planet, $techDefinition, $level);

    }



    private function persistQueue(Player $player): void
    {
        if (!isset($this->playerResearchQueues[$player->getId()])) {
            return;
        }

        $manager = $this->managerRegistry->getManager('world');

        QueueUtil::forEach(
            $this->playerResearchQueues[$player->getId()],
            fn(QueueJob $job) => $manager->persist($job));

    }



    public function canBeResearched(Planet $planet, string $techName, ?int $level = null, ?ResourcePack $cost = null ): bool
    {
        $researchConfig = $this->researchTechRegistry->get($techName);


        $level ??= $this->getNextLevelForResearch($planet->getPlayer(), $techName);
        $cost ??= $this->getCostForResearch($planet, $techName, $level);

        $playerTech = $this->playerTechRepository->findByPlayer($planet->getPlayer());
        foreach ($researchConfig->getRequires() as $requirements) {
            if (isset($requirements['techs'])) {
                $buildingRequirements = new BuildingRequirements($requirements['buildings']);
                if (!$buildingRequirements->isSatisfied(PlanetBuildingList::fromPlanet($planet))) {
                    return false;
                }
            }
            if (isset($requirements['techs'])) {
                $techRequirements = new TechRequirement($requirements['techs']);
                if (!$techRequirements->isSatisfied($playerTech)) {
                    return false;
                }
            }

        }

        $storage = $planet->getStorage();

        return $storage?->containResources($cost);
    }
}