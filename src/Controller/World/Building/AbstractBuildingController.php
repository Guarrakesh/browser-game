<?php

namespace App\Controller\World\Building;

use App\Entity\World\CampBuilding;
use App\Model\ViewModel\BaseViewModel;
use App\Model\ViewModel\BuildingViewModel;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


abstract class AbstractBuildingController extends AbstractController implements BuildingControllerInterface
{

    public function __construct(protected ServiceLocator $actions)
    {
    }

    abstract public static function getType(): string;

    public function handle(Request $request, CampBuilding $building): Response
    {
        $viewModel = $this->executeAction($request, $building);

        if ($viewModel->response) {
            return $viewModel->response;
        }

        $viewModel->camp ??= $building->getCamp();
        $viewModel->building ??= $building;

        $template = $viewModel->template ?? $this->getDefaultTemplate();

        $this->prepareViewModel($viewModel);

        if ($template) {
            return $this->render($template, [
                'building' => $viewModel->building,
                'camp' => $viewModel->camp,
                'view' => $viewModel,
            ]);
        }

        throw new LogicException("Action Executed did not return neither a template nor a response.");
    }


    /**
     * @param Request $request
     * @param CampBuilding $building
     * @return BuildingViewModel
     */
    protected function executeAction(Request $request, CampBuilding $building): BuildingViewModel
    {
        $action = $request->get('action') ?? $this->getDefaultAction();

        if ($action && $this->actions->has($action)) {
            $action = $this->actions->get($action);
            return $action->execute($request, $building);
        }

        throw new \LogicException(sprintf("Action %s did not return a view model", $action));


    }

    /** @return class-string<BuildingActionInterface>|null */
    abstract protected function getDefaultAction(): ?string;

    abstract protected function getDefaultTemplate(): ?string;

    protected function prepareViewModel(BaseViewModel $viewModel): void
    {}
}