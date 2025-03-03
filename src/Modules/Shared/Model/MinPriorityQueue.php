<?php

namespace App\Modules\Shared\Model;

use App\Modules\Shared\Model\Entity\QueueJob;
use Doctrine\Common\Collections\Collection;
use SplPriorityQueue;

class MinPriorityQueue extends SplPriorityQueue
{
    public function compare(mixed $priority1, mixed $priority2): int
    {
        if ($priority1 == $priority2) return 0;

        return $priority1 > $priority2 ? 1 : -1;
    }

    /**
     * @param Collection<QueueJob> $collection
     * @return SplPriorityQueue
     */
    public static function fromCollection(Collection $collection): SplPriorityQueue
    {
        $self = new self();
        foreach ($collection as $job) {
            $self->insert($job, $job->getStartedAt());
        }
        $self->setExtractFlags(SplPriorityQueue::EXTR_DATA);

        return $self;
    }


}