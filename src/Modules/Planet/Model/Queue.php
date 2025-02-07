<?php

namespace App\Modules\Planet\Model;

use App\Modules\Planet\Model\Entity\QueueJob;
use Countable;
use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * A base class that provides basic methods for a Queue.
 * @template T
 */
abstract class Queue implements Countable
{
    /** @var QueueJob[] */
    private array $jobs;

    public function __construct(array $jobs = [])
    {
        $cloned = [...$jobs];
        uasort($cloned, fn(QueueJob $a, QueueJob $b) => $a->getStartedAt() <=> $b->getStartedAt());

        $this->jobs = $cloned;

    }

    /**
     * @return T|null
     */
    protected function top(): ?QueueJob
    {
        return $this->jobs[0] ?? null;
    }

    /**
     * @return T|null
     */
    protected function bottom(): ?QueueJob
    {
        return $this->jobs[count($this->jobs)-1] ?? null;
    }

    /**
     * @param T $job
     * @param int $duration
     * @return void
     */
    protected function enqueueJob(QueueJob $job, int $duration): void
    {
        /** @var QueueJob $bottom */
        $bottom = $this->jobs[count($this->jobs)-1] ?? null;

        $currentTime = $bottom ? $bottom->getCompletedAt() : new DateTimeImmutable();
        $job->setStartedAt($currentTime);

        $completionTime = $currentTime->add(new DateInterval("PT{$duration}S"));
        $job->setCompletedAt($completionTime);

        $this->jobs[] = $job;

    }

    /** @return T */
    protected function dequeueJob(): QueueJob
    {
        return array_shift($this->jobs);
    }

    /**
     * @param T $job
     */
    protected function cancelJob(QueueJob $job): void
    {
        $index = 0;
        foreach ($this->jobs as $qJob) {
            if ($qJob !== $job) {
                $index++;
                continue;
            }

            // all next jobs are updated.
            unset($this->jobs[$index]);
            $previousCompletedAt = $index === 0 ? new DateTimeImmutable() : $this->jobs[$index - 1]->getCompletedAt();
            $index++;
            while ($index <= count($this->jobs)) {
                $current = $this->jobs[$index];

                $this->updateJobBasedOnPrevious($current, $previousCompletedAt);
                $previousCompletedAt = $current->getCompletedAt();
                $index++;
            }
            $this->jobs = array_values($this->jobs);
            break;
        }

    }

    protected function moveJob(int $from, int $to): void
    {
        if ($from >= $this->count()) {
            throw new InvalidArgumentException(sprintf("Index %s out of range", $from));
        }

        $job = $this->jobs[$from];

        if ($job->getStartedAt() < new DateTimeImmutable()) {
            throw new InvalidArgumentException(sprintf("Could not move Job #%d because it's already started", $job->getId()));
        }

        if ($this->jobs[$to]->getStartedAt() < new DateTimeImmutable()) {
            throw new InvalidArgumentException(sprintf("Could not move Job from position %d to %d as a job in position %d has already started", $from, $to, $to));
        }
        if ($to > count($this->jobs)) {
            throw new InvalidArgumentException(sprintf("Tried to move a Job outside to position %d the range of the queue (0..%d)", $to, count($this->jobs) - 1));
        }

        $out = array_splice($this->jobs, $from, 1);
        array_splice($this->jobs, $to, 0, $out);


        // Update all jobs timestamp affected by this movement.
        for ($i = min($from, $to); $i<count($this->jobs); $i++) {
            $previous = $i > 0 ? $this->jobs[$i-1]->getCompletedAt() : new DateTimeImmutable();

            $current = $this->jobs[$i];
            $this->updateJobBasedOnPrevious($current, $previous);
        }

    }

    private function updateJobBasedOnPrevious(QueueJob $job, DateTimeImmutable $completedAt): void
    {
        $duration = $job->getCompletedAt()->getTimestamp() - $job->getStartedAt()->getTimestamp();

        $job->setStartedAt($completedAt);
        $job->setCompletedAt($completedAt->add(new DateInterval("PT{$duration}S")));

    }



    /**
     * @return T[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function count(): int
    {
        return count($this->jobs);
    }

}