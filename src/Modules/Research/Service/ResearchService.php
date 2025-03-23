<?php

namespace App\Modules\Research\Service;

use App\Entity\World\Player;
use App\Exception\GameException;
use App\Helper\QueueUtil;
use App\Helper\TransactionTrait;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Service\PlanetService;
use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;
use App\Modules\Research\Dto\ResearchCenterDTO;
use App\Modules\Research\Dto\ResearchQueueJobDTO;
use App\Modules\Research\Dto\ResearchTechDTO;
use App\Modules\Research\Infra\Registry\ResearchTechRegistry;
use App\Modules\Research\Infra\Repository\PlayerTechRepository;
use App\Modules\Research\Infra\Repository\ResearchQueueRepository;
use App\Modules\Research\Model\DomainService\ResearchRequirementsDomainService;
use App\Modules\Research\Model\Entity\ResearchQueueJob;
use App\Modules\Research\Model\ResearchQueue;
use App\Modules\Shared\Constants;
use App\Modules\Shared\Dto\GameObjectWithRequirements;
use App\Modules\Shared\Exception\InsufficientResourcesException;
use App\Modules\Shared\Model\Entity\QueueJob;
use App\Modules\Shared\Model\ResourcePack;
use App\Modules\Shared\Service\Cost\CostCalculator;
use App\Modules\Shared\Service\ObjectTime\ObjectTimeService;
use App\Service\StorageService;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Throwable;

class ResearchService
{
    use TransactionTrait;

    public function __construct(
        private readonly ResearchQueueRepository           $researchQueueJobRepository,
        private readonly ResearchTechRegistry              $researchTechRegistry,
        private readonly CostCalculator                    $costService,
        private readonly ManagerRegistry                   $managerRegistry,
        private readonly StorageService                    $storageService,
        private readonly PlayerTechRepository              $playerTechRepository,
        private readonly ObjectTimeService                 $objectTimeService,
        private readonly Security                          $security,
        private readonly ResearchRequirementsDomainService $researchRequirementsDomainService,
        private readonly PlanetService                     $planetService,
    )
    {
    }

    public function getResearchCenterOverview(int $planetId): ResearchCenterDTO
    {

        $researchCenterDto = new ResearchCenterDTO();
        $planet = $this->planetService->getPlanet($planetId);
        $playerId = $planet->playerId;


        $queue = $this->researchQueueJobRepository->getResearchQueue($playerId);

        /** @var array<GameObjectWithRequirements> $techs */
        $techs = [];
        foreach ($this->researchTechRegistry->getAll() as $techDef) {
            $tech = new GameObjectWithRequirements($techDef->getAsGameObject(), $techDef->getRequirements());
            $techs[$techDef->getName()] = $tech;
        }

        $researchCenterDto->techs = $techs;
        $playerTechs = $this->playerTechRepository->findByPlayerAssociative($playerId);
        $researchCenterDto->playerTechs = array_map(static fn($tech) => $tech->getTechName(), $playerTechs);
        $researchCenterDto->possibleResearches = $this->getPossibleResearches($playerTechs, $queue, $planet);
        $researchCenterDto->queuedJobs = $this->getResearchQueueJobs($playerId, $planet);

        foreach ($techs as $tech) {
            $techName = $tech->object->getName();
            if (!$queue->hasTech($techName)
                && !array_key_exists($techName, $playerTechs)
                && !array_key_exists($techName, $researchCenterDto->possibleResearches
                )) {
                $researchCenterDto->lockedResearches[$techName] = $tech;
            }
        }
        return $researchCenterDto;
        //  $tech->satisfied = $this->researchService->canBeResearched($planet, $techDef->getName());
    }

    public function enqueueResearch(int $planetId, string $techName): ResearchCenterDTO
    {

        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();

        $manager->wrapInTransaction(function () use ($planetId, $techName, $manager) {
            $planet = $this->planetService->getPlanet($planetId);

            $playerId = $planet->playerId;
            $techDefinition = $this->researchTechRegistry->get($techName);


            $planetBuildings = $this->planetService->getPlanetBuildings($planetId);
            $playerTechs = $this->playerTechRepository->findByPlayerAssociative($playerId);

            $requirementsMet = $this->researchRequirementsDomainService->areResearchRequirementsMet($techDefinition, $playerTechs, $planetBuildings);
            if (!$requirementsMet) {
                throw new GameException("Research Requirements not met.");
            }

            $cost = $this->getCostForTech($techDefinition, $planet);
            $costSatisfied = $this->planetService->planetHasResources($planetId, $cost);
            if (!$costSatisfied) {
                throw new InsufficientResourcesException($cost);
            }

            $queue = $this->researchQueueJobRepository->getResearchQueue($playerId);

            if ($queue->hasTech($techName)) {
                throw new GameException(sprintf("Tech %s is already being researched.", $techName));
            }
            $researchTime = $this->getResearchTimeForTech($techDefinition, $planet, $cost);
            $job = new ResearchQueueJob($playerId, $planetId, $techName, $researchTime, $cost);

            $queue->enqueue($techDefinition, $job, $researchTime);
            $this->planetService->debitResources($planetId, $cost);

            $manager->persist($job);
            $manager->flush();

        });

        return $this->getResearchCenterOverview($planetId);


    }


    public function cancelResearch(int $planetId, int $researchJobId): ResearchCenterDTO
    {
        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();

        $researchJob = $this->researchQueueJobRepository->find($researchJobId);
        if (!$researchJobId) {
            return $this->getResearchCenterOverview($planetId);
        }

        $queue = $this->researchQueueJobRepository->getResearchQueue($researchJob->getPlayerId());

        $manager->wrapInTransaction(function () use ($queue, $planetId, $researchJob, $manager) {
            $queue->cancel($researchJob);
            $this->planetService->debitResources($researchJob->getPlanetId(), $researchJob->getResourcesUsed());
            $manager->flush();
        });

        return $this->getResearchCenterOverview($planetId);
    }

    /**
     * @return ResearchQueueJobDTO[]
     * @throws Exception
     */
    public function getResearchQueueJobs(int $playerId, ?PlanetDTO $planet = null): array
    {
        $queue = $this->researchQueueJobRepository->getResearchQueue($playerId, $planet?->id);

        return array_map(
            fn(QueueJob $job) => new ResearchQueueJobDTO($job->getId(), $job->getTechName(), $job->getDuration(), $job->getStartedAt(), $job->getCompletedAt()),
            $queue->getJobs()
        );
    }

    public function getCostForTech(ResearchTechDefinitionInterface $definition, PlanetDTO $planetDTO): ResourcePack
    {
        return $this->costService->getCostForObject($definition, null);
    }

    public function getResearchTimeForTech(ResearchTechDefinitionInterface $definition, PlanetDTO $planetDTO, ResourcePack $cost): int
    {
        return $this->objectTimeService->getTimeForObject($planetDTO->id, $planetDTO->buildings, $definition->getAsGameObject(), null, $cost);
    }

    private function getPlanetResearchCenterLevel(PlanetDTO $planetDTO): ?int
    {
        return $planetDTO->buildings[Constants::RESEARCH_CENTER]?->getLevel();
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


    /**
     * Return all possible ResearchDTO
     * @return array<ResearchTechDTO>
     */
    public function getPossibleResearches(array $playerTechs, ResearchQueue $researchQueue, PlanetDTO $planet): array
    {
        $result = [];
        $planetId = $planet->id;
        foreach ($this->researchTechRegistry->getAll() as $techName => $definition) {
            if (!$this->researchRequirementsDomainService->areResearchRequirementsMet($definition, $playerTechs, $planet->buildings)) {
                continue;
            }
            if ($researchQueue->hasTech($techName)) {
                continue;
            }
            if (array_key_exists($techName, $playerTechs)) {
                continue;
            }
            // $cost = $this->costService->getCostForObject()
            $cost = $this->getCostForTech($definition, $planet);
            $buildTime = $this->objectTimeService->getTimeForObject($planet->id, $planet->buildings, $definition->getAsGameObject(), null, $cost);
            $tech = new ResearchTechDTO(
                $techName,
                $cost,
                $buildTime,
                $this->planetService->planetHasResources($planetId, $cost),
                $definition->getDescription(),
            );

            $result[$techName] = $tech;
        }

        return $result;
    }

}