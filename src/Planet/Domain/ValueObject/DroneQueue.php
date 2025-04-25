<?php

namespace App\Planet\Domain\ValueObject;

use App\Planet\Domain\Entity\Drone\DroneQueueJob;
use App\Planet\Domain\Entity\PlanetConstruction;
use App\Shared\Model\Queue;
use Symfony\Component\Clock\Clock;

/**
 * @implements Queue<DroneQueueJob>
 */
class DroneQueue extends Queue
{
    public function enqueue(DroneQueueJob $job, int $duration): void
    {
        $this->enqueueJob($job, $duration);
//        foreach ($this->getJobs() as $job) {
//            if ($job->getBuildingName() !== $buildingDefinition->getName()) {
//                continue;
//            }
//            // Invariant: a Construction and a Demolition of the same building can't co-exist in the queue.
//            if ($isDowngrade && !$job->isDowngrade()) {
//                throw new EnqueueException("Cannot downgrade. An upgrade is already in queue.");
//            } elseif (!$isDowngrade && $job->isDowngrade()) {
//                throw new EnqueueException("Cannot upgrade. A downgrade is already in queue.");
//            }
//        }
//        $this->enqueueJob($construction, $buildTime);
    }

    public function terminate(PlanetConstruction $construction): void
    {
        $construction->setCompletedAt(Clock::get()->now());
        $construction->markAsProcessed();
    }



}