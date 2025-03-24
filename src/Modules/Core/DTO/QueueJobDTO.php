<?php

namespace App\Modules\Core\DTO;

use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Clock\Clock;

class QueueJobDTO
{
    public int $id;

    public int $duration;
    public DateTimeImmutable $completedAt;
    public DateTimeImmutable $startedAt;
    public ?DateTimeImmutable $cancelledAt = null;


    public function __construct(int $id, int $duration, DateTimeImmutable $completedAt, DateTimeImmutable $startedAt, ?DateTimeImmutable $cancelledAt)
    {
        $this->id = $id;
        $this->duration = $duration;
        $this->completedAt = $completedAt;
        $this->startedAt = $startedAt;
        $this->cancelledAt = $cancelledAt;
    }


    /**
     * @return int A number, between 0 and 100, indicating the completion progress
     */
    public function getProgress(): int
    {
        $now = Clock::get()->now();
        if ($this->completedAt < $now) {
            return 1;
        }

        $elapsed = $now->getTimestamp() - $this->startedAt->getTimestamp();
        $result = $elapsed / $this->duration;
        return round(100 * $result , 2);
    }


    public function remainingTime(): \DateInterval
    {
        return $this->completedAt->diff(new DateTimeImmutable());
    }

}