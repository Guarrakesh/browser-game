<?php

namespace App\Modules\Research\DTO;

class ResearchQueueDTO
{
    public array $jobs = [];

    public int $planetId;

    public function setJobs(array $jobs): ResearchQueueDTO
    {
        $this->jobs = $jobs;
        return $this;
    }

    public function setPlanetId(int $planetId): ResearchQueueDTO
    {
        $this->planetId = $planetId;
        return $this;
    }


}