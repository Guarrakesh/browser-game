<?php

namespace App\Helper;

use App\Entity\World\Queue\Queue;
use App\Entity\World\Queue\QueueJob;

class QueueUtil
{
    /**
     * @param Queue $queue
     * @param callable $callback The filter callback
     * @return QueueJob[]
     */
    public static function filter(Queue $queue, callable $callback): array
    {
        $filtered= [];
        foreach ($queue->getJobs() as $job) {
            if ($callback($job)) {
                $filtered[] = $job;
            }
        }

        return $filtered;
    }

    public static function findLast(Queue $queue, callable $callback): ?QueueJob
    {
        $filtered = static::filter($queue, $callback);

        return $filtered ? $filtered[count($filtered) - 1] : null;
    }


    public static function findFirst(Queue $queue, callable $callback): ?QueueJob
    {
        $filtered = static::filter($queue, $callback);

        return $filtered ? $filtered[0] : null;
    }

    public static function forEach(Queue $queue, callable $callback): void
    {
        foreach ($queue->getJobs() as $job) {
            call_user_func($callback, $job);
        }
    }
}