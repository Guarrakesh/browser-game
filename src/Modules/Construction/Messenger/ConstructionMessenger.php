<?php

namespace App\Modules\Construction\Messenger;

use App\Modules\Construction\DTO\ConstructionDTO;
use App\Modules\Construction\DTO\ConstructionQueueDTO;
use App\Modules\Construction\DTO\ConstructionQueueJobDTO;
use App\Modules\Construction\DTO\EnqueueConstructionRequestDTO;
use App\Modules\Construction\DTO\PossibleConstructionsDTO;
use App\Modules\Construction\Service\ConstructionService;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\Entity\Planet;
use App\Modules\Core\Repository\PlanetRepository;
use AutoMapper\AutoMapperInterface;

readonly class ConstructionMessenger
{
    public function __construct(
        private AutoMapperInterface $autoMapper,
        private ConstructionService $constructionService, private PlanetRepository $planetRepository)
    {

    }

    public function sendEnqueueConstructionRequest(EnqueueConstructionRequestDTO $request): ConstructionQueueDTO
    {
        $planet = $this->planetRepository->find($request->planetId);

        $queue = $this->constructionService->enqueueConstruction($planet, $request->building);


        $queueDto = new ConstructionQueueDTO();
        return $this->autoMapper->map($queue, $queueDto);
    }

    public function sendGetConstructionQueueRequest(PlanetDTO $planetDTO): ConstructionQueueDTO
    {
        $planet = $this->planetRepository->find($planetDTO->id);

        $queue = $this->constructionService->getConstructionQueue($planet);

        /** @var ConstructionQueueDTO $queueDto */
        $dto = new ConstructionQueueDTO();
        $dto = $this->autoMapper->map($queue, $dto, ['jobClass' => ConstructionQueueJobDTO::class]);
        $dto->planetId = $planet->getId();

        return $dto;
    }


    /**
     * @return array<ConstructionDTO>
     */
    public function sendGetPossibleConstructionsRequest(PlanetDTO $planetDTO): PossibleConstructionsDTO
    {
        $planet = $this->planetRepository->find($planetDTO->id);

        return new PossibleConstructionsDTO($planetDTO, $this->constructionService->getPossibleConstructions($planet));
    }

}