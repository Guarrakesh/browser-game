<?php

namespace App\Service\Cost;

use App\Entity\World\Camp;
use App\Event\CostEvent;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\Service\UniverseSettingsService;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class CostService
{

    /**
     * @param iterable<CostCalculatorInterface> $costCalculators
     */
    public function __construct(
        #[AutowireIterator(CostCalculatorInterface::class)] private readonly iterable $costCalculators, private readonly EventDispatcherInterface $eventDispatcher, private readonly UniverseSettingsService $universeSettingsService
    )
    {
    }

    public function getCostForObject(Camp $camp, BaseDefinitionInterface $definition, ?int $level, $context = []): ResourcePack
    {
        $context ??= []; // TODO: should this be created here or passed along?
        $calculator = $this->getCalculator($definition, $level, $context);
        if ($calculator) {
            $baseCost = $calculator->getCost($camp, $definition, $level, $context)
                ->multiply($this->universeSettingsService->getUniverseSpeed());
        } else {
            if (!$definition->findParameter('cost_factor')) {

                throw new \LogicException(sprintf("Can't get a way to calculate the cost for %s:%s", $definition->getType()->name, $definition->getName()));
            }
            $baseCost= $definition->getBaseCost()->multiply(
                $definition->findParameter('cost_factor') ** ($level -1)
            );
        }


        $event = new CostEvent($camp, $definition, $level, $baseCost, $context);
        $this->eventDispatcher->dispatch($event);
        return $event->getCost();
    }

    private function getCalculator(BaseDefinitionInterface $definition, ?int $level, $context = []): ?CostCalculatorInterface
    {
        foreach ($this->costCalculators as $calculator) {
            if ($calculator->supports($definition, $level, $context)) {
                return $calculator;
            }
        }

        return null;
    }
}