<?php

namespace App\Controller\World\Building\ControlHub;

use App\Constants;
use App\Controller\World\Building\AbstractBuildingController;
use App\Controller\World\Building\BuildingActionInterface;
use App\Controller\World\Building\ControlHub\Action\IndexAction;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ControlHubController extends AbstractBuildingController
{

    /**
     * @param ServiceLocator<BuildingActionInterface> $actions
     */
    public function __construct(
        protected ServiceLocator          $actions,
    )
    {
        parent::__construct($this->actions);
    }

    public static function getType(): string
    {
        return Constants::CONTROL_HUB;
    }

    protected function getDefaultAction(): ?string
    {
        return IndexAction::getName();
    }

    protected function getDefaultTemplate(): ?string
    {
        return "camp/building/control_hub/index.html.twig";
    }
}