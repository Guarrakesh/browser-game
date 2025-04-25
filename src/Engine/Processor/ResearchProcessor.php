<?php

namespace App\Engine\Processor;

use App\Research\Repository\ResearchQueueRepository;
use App\Research\Service\DomainService\ResearchProcessorDomainService;
use App\Shared\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ResearchProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly ManagerRegistry         $managerRegistry,
        private readonly PlayerRepository        $playerRepository,
        private readonly Security                $security,
        private readonly ResearchQueueRepository $researchQueueJobRepository,
        private readonly LoggerInterface         $logger,
        private readonly ResearchProcessorDomainService $researchDomainService
    )
    {
    }

    public function process(int $timestamp): void
    {
        $user = $this->security->getUser();
        if ($user) {
            $player = $this->playerRepository->findByUser($user);
            $this->updateResearchesForPlayer($timestamp, $player);
        }


    }

    private function updateResearchesForPlayer(int $timestamp, ?\App\Entity\World\Player $player)
    {
        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();

        $researchQueue = $this->researchQueueJobRepository->getResearchQueue($player->getId());

        foreach ($this->researchDomainService->processCompletedJobs($timestamp, $researchQueue) as $playerTech) {
            $manager->persist($playerTech);
        }

        $manager->flush();

    }

}