<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CampController extends AbstractController
{

    #[Route('/camp/new', name: 'camp_new')]
    public function newCamp(): Response
    {

    }
}
