<?php

namespace App\Modules\Research\Model\DomainService;

use App\Exception\GameException;
use App\Modules\Planet\Service\ConstructionProcessor;
use App\Modules\Research\Model\Entity\PlayerTech;
use App\Modules\Research\Model\Entity\ResearchQueueJob;
use App\Modules\Research\Model\ResearchQueue;

readonly class ResearchProcessorDomainService
{
    public function __construct()
    {
    }

    /**
     * @param int $timestamp
     * @param ResearchQueue $researchQueue
     * @param PlayerTech $playerTech
     * @return void
     */
    public function processCompletedJobs(int $timestamp, ResearchQueue $researchQueue): iterable
    {

        foreach ($researchQueue->processCompletedJobs($timestamp) as $researchJob) {
            /** @var ResearchQueueJob $researchJob */
            yield $this->processResearch($timestamp, $researchJob);
        }
    }

    /**
     *
     * @param int $timestamp
     * @param ResearchQueueJob $research
     * @param PlayerTech $playerTech
     * @return void
     */
    private function processResearch(int $timestamp, ResearchQueueJob $research): ?PlayerTech
    {
        if ($research->getCompletedAt()->getTimestamp() > $timestamp) {
            return null;
        }

        $tech = $research->getDefinition();
        if (!$tech) {
            throw new GameException(sprintf("Research Job #%d references to a tech '%s' that is not defined", $research->getId(), $research->getTechName()));
        }


        return new PlayerTech($research->getPlayerId(), $tech->getName(), $tech);
    }
}