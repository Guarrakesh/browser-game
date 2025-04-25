<?php

namespace App\Research\Model;

use App\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;
use App\Research\Model\Entity\ResearchQueueJob;
use App\Shared\Model\Queue;
use Symfony\Component\Clock\Clock;

/**
 * @phpstan-implements Queue<ResearchQueueJob>
 */
class ResearchQueue extends Queue
{
    public function enqueue(ResearchTechDefinitionInterface $definition, ResearchQueueJob $job, int $researchTime): void
    {
        if ($this->hasTech($definition->getName())) {
            return;
        }

        $this->enqueueJob($job, $researchTime);
    }

    public function cancel(ResearchQueueJob $job): ResearchQueueJob
    {
        $this->cancelJob($job);

        return $job;
    }

    public function terminate(ResearchQueueJob $job): void
    {
        $job->setCompletedAt(Clock::get()->now());
        $job->markAsProcessed();
    }

    public function hasTech(string $techName): bool
    {
        foreach ($this->getJobs() as $job) {
            if ($job->getTechName() === $techName) {
                return true;
            }
        }

        return false;
    }

}