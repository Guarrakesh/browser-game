<?php

namespace App\Service;

use App\Entity\World\Player;
use App\Exception\GameException;
use App\Helper\QueueUtil;
use App\Helper\TransactionTrait;
use App\Model\PlanetBuildingList;
use App\Model\TechRequirement;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Infra\Registry\ResearchTechRegistry;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\DomainService\Cost\CostCalculator;
use App\Modules\Planet\Model\DomainService\ObjectTime\ObjectTimeService;
use App\Modules\Planet\Model\Entity\QueueJob;
use App\Modules\Planet\Model\Exception\InsufficientResourcesException;
use App\Modules\Planet\Model\Queue;
use App\Modules\Planet\Model\ResearchQueueJob;
use App\Modules\Research\DTO\ResearchQueueDTO;
use App\Modules\Shared\Model\ResourcePack;
use App\Repository\PlayerRepository;
use App\Repository\PlayerTechRepository;
use App\Repository\ResearchQueueJobRepository;
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
        private readonly CostCalculator             $costService,
        private readonly ManagerRegistry            $managerRegistry,
        private readonly StorageService             $storageService,
        private readonly PlayerTechRepository       $playerTechRepository, private readonly ObjectTimeService $objectTimeService, private readonly PlanetRepository $planetRepository, private readonly PlayerRepository $playerRepository,
    )
    {
    }


    public function enqueueResearch(PlanetDTO $planetDTO, string $techName): void
    {


        $manager = $this->managerRegistry->getManager('world');
        $planet = $this->planetRepository->find($planetDTO->id);
        $player = $this->playerRepository->find($planetDTO->id);

        $storage = $planet->getStorage();

        $queue = $this->researchQueueJobRepository->getResearchQueue($player->getId(), $planet->getId());
        $callback = function () use ($manager, $storage, $player, $techName, $planet, $queue) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);

            $cost = $this->getCostForResearch($planet, $techName);
            $buildTime = $this->getResearchTime($planet, $planet->ply, $techName);

            if (!$this->canBeResearched($planet, $player->getId(), $techName, null, $cost)) {
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
        $planet = $job->getPlanet();
        $queue = $this->getResearchQueue($planet->getPlayer());
        $storage = $planet->getStorage();
        $callback = function () use ($manager, $job, $planet, $queue, $storage) {
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
    public function getResearchQueue(int $playerId, ?PlanetDTO $planet = null): ResearchQueueDTO
    {
        $queue = $this->researchQueueJobRepository->getResearchQueue($playerId, $planet->id);

        return (new ResearchQueueDTO())
            ->setJobs($queue->getJobs())
            ->setPlanetId($planet->id);
    }

    public function getCostForResearch(PlanetDTO $planet, string $techName, ?int $level = null): ResourcePack
    {
        $researchDefinition = $this->researchTechRegistry->get($techName);
        $level ??= $this->getNextLevelForResearch($planet->playerId, $techName);

        return $this->costService->getCostForObject($planet, $researchDefinition, $level);
    }

    public function getNextLevelForResearch(int $playerId, string $techName): int
    {
        $queue = $this->getResearchQueue($playerId);

        /** @var Queue<ResearchQueueJob> $existingResearchJobs */
        $existingResearchJobs = QueueUtil::filter($queue, fn(ResearchQueueJob $job) => $job->getTechName() == $techName);
        if (!empty($existingResearchJobs)) {
            return $existingResearchJobs[count($existingResearchJobs) - 1]->getLevel() + 1;
        }

        return ($this->playerTechRepository->findByPlayer($playerId)?->getLevel($techName) ?? 0) + 1;
    }

    public function getResearchTime(PlanetDTO $planet, int $playerId, string $techName): int
    {
        $level ??= $this->getNextLevelForResearch($playerId, $techName);
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


    public function canBeResearched(PlanetDTO $planet, string $techName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        $researchConfig = $this->researchTechRegistry->get($techName);
        $playerId = $planet->playerId;
        $planet = $this->planetRepository->find($planet->id);

        $level ??= $this->getNextLevelForResearch($playerId, $techName);
        $cost ??= $this->getCostForResearch($planet, $techName, $level);

        $playerTech = $this->playerTechRepository->findByPlayer($playerId);
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