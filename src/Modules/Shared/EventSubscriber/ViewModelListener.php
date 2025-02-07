<?php

namespace App\Modules\Shared\EventSubscriber;

use App\Exception\GameException;
use App\Modules\Core\ViewModel\BaseViewModel;
use App\Modules\Planet\Service\PlanetOverviewService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

class ViewModelListener implements EventSubscriberInterface
{

    public function __construct(
        #[Autowire('@serializer')] private readonly SerializerInterface $serializer,
        private readonly Environment                                    $twig, private readonly PlanetOverviewService $planetOverviewService
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 255]
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $viewModel = $event->getControllerResult();
        if (!$viewModel instanceof BaseViewModel || $event->hasResponse()) {
            return;
        }

        if (!$viewModel->planet) {
            $planetId = $event->getRequest()->attributes->get('planetId');
            if (!$planetId) {
                throw new GameException("Invalid request: planet not found");
            }
            $viewModel->planet = $this->planetOverviewService->getPlanetOverview($planetId);
        }


        if ($viewModel->response) {
            $event->setResponse($viewModel->response);
            $this->processMessages($viewModel, $event->getRequest());
            return;
        }


        $template = $viewModel->template;

        if ($template) {
            $response = new Response();
            $response->setContent($this->twig->render($template, [
                'planet' => $viewModel->planet,
                'view' => $viewModel,
            ]));
            $event->setResponse($response);
            $this->processMessages($viewModel, $event->getRequest());

        } else {
            //$response = new RedirectResponse($event->getRequest()->ge)
            $json = $this->serializer->serialize($viewModel, 'json');
            $event->setResponse(new JsonResponse($json, 200, [], true));

        }


    }

    protected function processMessages(BaseViewModel $viewModel, Request $request): void
    {
        $isXmlHttpRequest = $request->isXmlHttpRequest();
        if (!$viewModel->hasMessages() || $isXmlHttpRequest) {
            return;
        }

        try {
            $session = $request->getSession();
            if (!$session instanceof FlashBagAwareSessionInterface) {
                return;
            }
            foreach ($viewModel->getMessages() as $message) {
                [$type, $message] = $message;
                $session->getFlashBag()->add($type, $message);
            }
        } catch (SessionNotFoundException) {
        }


    }
}