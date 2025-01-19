<?php

namespace App\Model\ViewModel;

use App\Entity\World\Camp;
use Symfony\Component\HttpFoundation\Response;

class BaseViewModel
{

    public function __construct(public ?Camp $camp = null, public ?Response $response = null, public ?string $template = null)
    {
    }

}