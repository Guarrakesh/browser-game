<?php

namespace App\Modules\Research\EventSubscriber;

use App\Modules\Research\Model\Entity\ResearchQueueJob;
use App\Modules\Research\Model\TechDefinitionAwareInterface;
use App\Modules\Research\Service\ResearchTechRegistry;
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