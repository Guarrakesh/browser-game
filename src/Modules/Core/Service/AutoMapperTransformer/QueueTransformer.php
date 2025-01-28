<?php

namespace App\Modules\Core\Service\AutoMapperTransformer;


use App\Entity\World\Queue\PlanetConstruction;
use App\Entity\World\Queue\Queue;
use App\Modules\Construction\DTO\ConstructionQueueJobDTO;
use App\Modules\Core\DTO\QueueJobDTO;
use AutoMapper\Transformer\PropertyTransformer\PropertyTransformerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['automapper.property_transformer'])]
class QueueTransformer implements PropertyTransformerInterface
{
    public function transform(mixed $value, object|array $source, array $context): mixed
    {
        $jobClass = $context['jobClass'] ?? null;

        $result = [];
        /** @var Queue $source */
        foreach ($source->getJobs() as $job) {
            if ($jobClass === ConstructionQueueJobDTO::class) {
                /** @var PlanetConstruction $job */
                $dto = new ConstructionQueueJobDTO();
                $dto->level = $job->getLevel();
                $dto->buildingName = $job->getBuildingName();
            } else if ($jobClass === "App\Modules\Research\DTO\ResearchQueueJobDTO") {
                throw new \LogicException("Not implemented yet.");

            } else {
                $dto = new QueueJobDTO();
            }
            $dto->id = $job->getId();
            $dto->completedAt = $job->getCompletedAt();
            $dto->startedAt = $job->getStartedAt();
            $dto->cancelledAt = $job->getCancelledAt();
            $dto->remainingTime = $job->getRemainingTime();
            $result[] = $dto;
        }

        return $result;
    }

}