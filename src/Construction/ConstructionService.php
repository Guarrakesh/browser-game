<?php

namespace App\Construction;

use App\Camp\CampFacade;
use App\Entity\World\Camp;
use App\Entity\World\ConstructionLog;
use App\Entity\World\Queue\CampConstruction;
use App\Exception\GameException;
use App\Exception\InsufficientResourcesException;
use App\Helper\DBUtils;
use App\Repository\PlayerRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

readonly class ConstructionService
{
    public function __construct(
        private CampFacade                   $campFacade,
        private EventDispatcherInterface $dispatcher,
        private PlayerRepository             $playerRepository,
        private ManagerRegistry              $managerRegistry
    )
    {
    }

    /**
     * @param Camp $camp
     * @param string $buildingName
     * @return void
     */
    public function enqueueConstruction(Camp $camp, string $buildingName): void
    {
        $manager = $this->managerRegistry->getManager('world');
        $buildTime = $this->campFacade->getBuildTime($camp, $buildingName);

        $storage = $camp->getStorage();
        $callback = function () use ($manager, $storage, $buildingName, $camp, $buildTime) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);

            $cost = $this->campFacade->getCostForBuilding($camp, $buildingName);

            if (!$this->campFacade->canBeBuilt($camp, $buildingName, null, $cost)) {
                throw new InsufficientResourcesException($cost);
            }
            $storage->addResources($cost->multiply(-1), $this->campFacade->getMaxStorage($camp));

            $construction = $camp->addNewConstruction($buildingName, $camp->getNextLevelForBuilding($buildingName), $buildTime);

            $manager->persist($construction);
            $manager->persist($camp);
        };

        try {
            DBUtils::transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Throwable $exception) {
            throw new GameException("Could not enqueue the construction. Try again.", 0, $exception);
        }
    }

    public function cancelConstruction(CampConstruction $construction): void
    {
        $manager = $this->managerRegistry->getManager('world');

        if (!$construction->getId()) {
            throw new GameException("Construction is not persisted.");
        }

        try {
            $construction->getCamp()->dequeueConstruction($construction);
            $construction->getCamp()->adjustConstructionQueue();
            $log = ConstructionLog::fromCancelled($construction);
            $manager->persist($log);
        } catch (Exception $e) {
            throw new GameException("Could not cancel constructions. Try again.", 0, $e);
        }

        $manager->remove($construction);
        $manager->flush();
    }


}