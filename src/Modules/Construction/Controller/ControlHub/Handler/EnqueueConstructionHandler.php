<?php

namespace App\Modules\Construction\Controller\ControlHub\Handler;

use App\Constants;
use App\Exception\GameException;
use App\Modules\Construction\Controller\ActionEnum;
use App\Modules\Construction\Controller\AsBuildingAction;
use App\Modules\Construction\Controller\BuildingActionInterface;
use App\Modules\Construction\DTO\EnqueueConstructionRequestDTO;
use App\Modules\Construction\Messenger\ConstructionMessenger;
use App\Modules\Construction\Service\ConstructionService;
use App\Modules\Core\DTO\PlanetBuildingDTO;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\ViewModel\ControlHubViewModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsBuildingAction(Constants::CONTROL_HUB)]
#[Route('/control_hub/enqueue_construction', name: 'control_hub_enqueue_construction', methods: ['POST'])]
class EnqueueConstructionHandler implements BuildingActionInterface
{

    public function __construct(private readonly LoggerInterface $logger, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function __invoke(
        #[MapRequestPayload()] EnqueueConstructionRequestDTO $enqueueConstructionRequest,
        Request                                            $request,
        PlanetDTO                                          $planet,
        ConstructionMessenger                              $constructionMessenger): ControlHubViewModel
    {

        $building = $request->getPayload()->get('building');
        $building = $planet->buildings[$building];
        $model = new ControlHubViewModel($building, $planet);
        $enqueueConstructionRequest->planetId = $planet->id;
        try {

            $result = $constructionMessenger->sendEnqueueConstructionRequest($enqueueConstructionRequest);
            $model->constructionQueue = $result;
            $model->response = new RedirectResponse($this->urlGenerator->generate('control_hub_index'));

            //$model->addMessage('success', "The building has been enqueued");
        } catch (GameException $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            throw $e;
            //$model->addMessage('error', "Cannot enqueue construction");
        }

        return $model;
    }

    public static function getName(): string
    {
        return ActionEnum::EnqueueConstruction->value;
    }
}