<?php

namespace App\Modules\Planet\Model\Entity;

use App\Exception\GameException;
use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Planet\Model\Exception\EnqueueException;
use App\Modules\Planet\Model\Queue;
use Symfony\Component\Clock\Clock;

/**
 * This is considered an Entity Child for the Planet aggregate.
 * The jobs of the queue are PlanetConstructions, ordered by their startedAt timestamp.
 * @implements Queue<PlanetConstruction>
 */
class ConstructionQueue extends Queue
{

    /** @return iterable<PlanetConstruction> */
    public function processCompletedConstructions(int $timestamp): iterable
    {
        while (true) {
            $job = $this->top();
            if (!$job || $job->getCompletedAt()->getTimestamp() > $timestamp || $job->isProcessed()) {
                return null;
            }

            $job = $this->dequeueJob();
            $job->markAsProcessed();
            yield $job;

        }


    }


    public function enqueue(BuildingDefinition $buildingDefinition, QueueJob $construction, int $buildTime, bool $isDowngrade = false): void
    {
        foreach ($this->getJobs() as $job) {
            if ($job->getBuildingName() !== $buildingDefinition->getName()) {
                continue;
            }
            // Invariant: a Construction and a Demolition of the same building can't co-exist in the queue.
            if ($isDowngrade && !$job->isDowngrade()) {
                throw new EnqueueException("Cannot downgrade. An upgrade is already in queue.");
            } elseif (!$isDowngrade && $job->isDowngrade()) {
                throw new EnqueueException("Cannot upgrade. A downgrade is already in queue.");
            }
        }
        $this->enqueueJob($construction, $buildTime);
    }

    /**
     *
     * @param PlanetConstruction $construction
     * @return PlanetConstruction The actual construction being cancelled. If there are multiple levels for the same building in queue,
     * the latest enqueued one gets cancelled. If there is only one level for the same building in queue, that one is actually cancelled.
     */
    public function cancel(PlanetConstruction $construction): PlanetConstruction
    {

        // Handle reordering of the levels for the same building in the queue.
        // Example we have level 1,2,3,4 of the same building in queue:
        // - User cancels level 2
        // - What is actually returned as cancelled is level 4 (the last one)
        // while the others are adjusted with the new levels
        //

        $latestJob = $construction;
        $lastLevel = $latestJob->getLevel();
        foreach ($this->getJobs() as $job) {
            if ($job->getBuildingName() !== $construction->getBuildingName()) {
                continue;
            }
            if ($job->isDowngrade() != $construction->isDowngrade()) {
                continue;
            }

            if ($job->getStartedAt() > $latestJob->getStartedAt()) {
                $job->setLevel($lastLevel);
                $lastLevel ++ ;
                $latestJob = $job;
            }
        }

        $this->cancelJob($construction);
        $construction->setCancelledAt(Clock::get()->now());

        return $latestJob;
    }

    /**
     * @param int $constructionId
     * @return PlanetConstruction
     * @throws GameException
     */
    public function getConstructionById(int $constructionId): PlanetConstruction
    {
        foreach ($this->getJobs() as $job) {
            if ($job->getId() === $constructionId) {
                return $job;
            }
        }

        throw new GameException(sprintf("No Construction with ID %s found.", $constructionId));
    }

    public function getUpgradeCountsForBuilding(string $buildingName): int
    {
        $count = 0;
        foreach ($this->getJobs() as $job) {
            if ($job->getBuildingName() === $buildingName && $job->isDowngrade() === false) {
                $count++;
            }
        }

        return $count;
    }

    public function getDowngradeCountsForBuilding(string $buildingName): int
    {
        $count = 0;
        foreach ($this->getJobs() as $job) {
            if ($job->getBuildingName() === $buildingName && $job->isDowngrade()) {
                $count++;
            }
        }

        return $count;
    }



}