<?php

namespace App\Engine\Processor;

use App\Modules\Planet\Infra\Registry\ResearchTechRegistry;
use App\Modules\Planet\Model\ResearchQueueJob;
use App\Repository\PlayerRepository;
use App\Repository\PlayerTechRepository;
use App\Repository\ResearchQueueJobRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ResearchProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly ManagerRegistry  $managerRegistry,
        private readonly PlayerRepository $playerRepository,
        private readonly Security         $security, private readonly PlayerTechRepository $playerTechRepository, private readonly ResearchQueueJobRepository $researchQueueJobRepository, private readonly ResearchTechRegistry $researchTechRegistry, private readonly LoggerInterface $logger
    )
    {
    }

    public function process(int $timestamp): void
    {
        $manager = $this->managerRegistry->getManager('world');
        $user = $this->security->getUser();
        if ($user) {
            $player = $this->playerRepository->findByUser($user);
            $this->updateResearchesForPlayer($timestamp, $player);
        }


    }

    private function updateResearchesForPlayer(int $timestamp, ?\App\Entity\World\Player $player)
    {
        $manager = $this->managerRegistry->getManager('world');
        $researches = $this->researchQueueJobRepository->getCompletedResearches($timestamp, $player);
        foreach ($researches as $research) {
            $this->processResearch($timestamp, $research);
        }
    }

    private function processResearch(int $timestamp, ResearchQueueJob $research )
    {
        if ($research->getCompletedAt()->getTimestamp() > $timestamp) {
            return;
        }

        $tech = $this->researchTechRegistry->find($research->getTechName());
        if (!$tech) {
            $this->logger->error(sprintf("Research Job #%d references to a tech '%s' that is not defined", $research->getId(), $research->getTechName()));
            return;
        }


        $techData = $this->playerTechRepository->findByPlayer($research->getPlayer());
        $currentLevel = $techData->getLevel($research->getTechName());
        // Sanity Check
        if ($currentLevel + 1 != $research->getLevel()) {
            // Level in the record is different from (current Level + 1), cancel the research.
            $research->setCancelledAt(new \DateTimeImmutable());
        }
        $techData->incrementLevel($research->getTechName());

    }
}