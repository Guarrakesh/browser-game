<?php

namespace App\Modules\Construction\Controller;

enum ActionEnum: string
{
    case IndexAction = 'index_action';
    case CancelConstruction = 'cancel_construction';
    case EnqueueResearch = 'enqueue_research';
    case EnqueueDrone = 'enqueue_drone';

    case CancelResearch = 'cancel_research';
    case EnqueueConstruction = 'enqueue_construction';
    case ListResearchTechs = 'list_research_techs';

}
