<?php

namespace App\Controller\World\Building\ResearchCenter;

use App\Constants;
use App\Controller\World\Building\AbstractBuildingController;
use App\Controller\World\Building\ResearchCenter\Action\ListTechsAction;
use App\Model\ViewModel\BaseViewModel;
use App\ObjectRegistry\ResearchTechRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ResearchCenterController extends AbstractBuildingController
{

    public function __construct(ServiceLocator $actions)
    {
        parent::__construct($actions);
    }

    public static function getType(): string
    {
        return Constants::RESEARCH_CENTER;
    }

    protected function getDefaultAction(): string
    {
        return ListTechsAction::getName();
    }

    protected function getDefaultTemplate(): ?string
    {
        return 'camp/buildings/research_center/index.html.twig';
    }

    protected function prepareViewModel(BaseViewModel $viewModel): void
    {

    }
}