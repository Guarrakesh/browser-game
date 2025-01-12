<?php

namespace App\Tests\Unit\Queue;

use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\QueueJob;
use DateTimeImmutable;
use Monolog\Test\TestCase;

class QueueTest extends TestCase
{
    private function getJobs(): array
    {
        $jobs = [];
        $duration = 10;
        $prevCompletion = new DateTimeImmutable();
        for ($i = 0; $i<10; $i++) {
            $job = new QueueJob();


            $job->setStartedAt($prevCompletion);
            $job->setCompletedAt($job->getStartedAt()->add(new \DateInterval('PT'.$duration.'S')));
            $prevCompletion = $job->getCompletedAt();

            $jobs[] = $job;
        }

        return $jobs;
    }
    public function testEnqueue()
    {
        $queue = new Queue($this->getJobs());

        $prevLastJob = $queue->bottom();

        $job = new QueueJob();
        $queue->enqueueJob($job, 10);
        $this->assertNotEquals($prevLastJob, $queue->bottom());
        $this->assertEquals($job, $queue->bottom());
        $this->assertEquals($prevLastJob->getCompletedAt(), $queue->bottom()->getStartedAt());
        $this->assertEquals($prevLastJob->getCompletedAt()->add(new \DateInterval('PT10S')), $queue->bottom()->getCompletedAt());

    }

    public function testMultipleEnqueues()
    {
        $queue = new Queue($this->getJobs());

        $prevLastJob = $queue->bottom();

        foreach (range(0, 5) as $i) {
            $job = new QueueJob();
            $queue->enqueueJob($job, 10);
        }

        $this->assertEquals($prevLastJob->getCompletedAt()->add(new \DateInterval('PT60S')), $queue->bottom()->getCompletedAt());
    }

    public function testDequeue()
    {
        $queue = new Queue($this->getJobs());

        $count = $queue->count();
        $dequeued = $queue->dequeueJob();
        $this->assertEquals($dequeued->getCompletedAt(), $queue->top()->getStartedAt());
        $this->assertEquals($count-1, $queue->count());
    }

    public function testCancelBottomJob()
    {
        $queue = new Queue($this->getJobs());
        $timestamps = [];
        foreach ($queue->getJobs() as $index => $job) {
            $timestamps[$index] = $job->getStartedAt();
        }


        $queue->cancelJob($queue->bottom());
        array_pop($timestamps);
        // Assert timestamps are unchanged
        $constraint = static::callback(function (Queue $queue) use ($timestamps) {
            foreach ($timestamps as $index => $timestamp) {
                if ($timestamp !== $queue->getJobs()[$index]->getStartedAt()) {
                    static::fail(sprintf("Timestamps do not match at index %d. %s = %s",
                        $index,
                        $timestamp->format('Y-m-d H:i:s'),
                        $queue->getJobs()[$index]->getStartedAt()->format('Y-m-d H:i:s')
                    ));
                }
            }

            return true;
        });
        static::assertThat($queue, $constraint, "Timestamps are unchanged");

    }


    public function testCancelSecondJob()
    {
        $queue = new Queue($this->getJobs());
        $secondJob = $queue->getJobs()[1];
        $timestamps = [];
        foreach ($queue->getJobs() as $index => $job) {
            $timestamps[$index] = $job->getStartedAt();
        }


        $third = $queue->getJobs()[2];
        $thirdTimestamp = $third->getStartedAt()->getTimestamp();
        $lastTimestamp = $queue->bottom()->getStartedAt();

        $queue->cancelJob($secondJob);

        // Assert third job moved as second one
        $this->assertEquals($third->getStartedAt(), $queue->getJobs()[1]->getStartedAt());
        $this->assertNotEquals($thirdTimestamp, $third->getStartedAt()->getTimestamp());
        $this->assertNotEquals($lastTimestamp->getTimestamp(), $queue->bottom()->getStartedAt()->getTimestamp());

    }




}