<?php

namespace App\Research\EventSubscriber;

use App\Research\Model\Entity\ResearchQueueJob;
use App\Research\Model\TechDefinitionAwareInterface;
use App\Research\Service\ResearchTechRegistry;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postLoad, method: 'postLoad', entityManager: 'world', entity: ResearchQueueJob::class)]
readonly class DoctrineResearchQueueJobPostLoadListener
{
    public function __construct(private ResearchTechRegistry $registry)
    {
    }

    public function postLoad(TechDefinitionAwareInterface $object, PostLoadEventArgs $event)
    {
        $definition = $object->getDefinition();
        if ($definition) {
            return;
        }

        $definition = $this->registry->find($object->getTechName());
        if (!$definition) {
            return;
        }
        $object->setDefinition($definition);

    }
}