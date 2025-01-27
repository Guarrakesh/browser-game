<?php

namespace App\Service\Cost;

use App\Helper\MemoizerTrait;
use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\Service\UniverseSettingsService;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class CostService
{
    use MemoizerTrait;

    /**
     * @param iterable<CostCalculatorInterface> $costCalculators
     * @param iterable<CostEffectInterface> $costEffects
     */
    public function __construct(
        #[AutowireIterator(CostEffectInterface::class)] private readonly iterable     $costEffects,
        #[AutowireIterator(CostCalculatorInterface::class)] private readonly iterable $costCalculators,
        private readonly UniverseSettingsService                                      $universeSettingsService
    )
    {
    }

    public function getCostForObject(Planet $planet, BaseDefinitionInterface $definition, ?int $level): ResourcePack
    {

        $key = spl_object_hash($planet) . '$' . spl_object_hash($definition) . '$' . $level;
        return $this->memoize($key, function () use ($planet, $definition, $level) {
            $context ??= [];
            $calculator = $this->getCalculator($definition, $level);
            if ($calculator) {
                $cost = $calculator->getCost($planet, $definition, $level)
                    ->multiply($this->universeSettingsService->getUniverseSpeed());
            } else {
                if (!$definition->findParameter('cost_factor')) {
                    throw new \LogicException(sprintf("Can't get a way to calculate the cost for %s:%s", $definition->getType()->name, $definition->getName()));
                }
                $cost = $definition->getBaseCost()->multiply(
                    $definition->findParameter('cost_factor') ** ($level - 1)
                );
            }


            foreach ($this->costEffects as $effect) {
                $cost = $effect->processCost($planet, $definition, $level);
            }

            return $cost;
        });


    }


    private function getCalculator(BaseDefinitionInterface $definition, ?int $level): ?CostCalculatorInterface
    {
        foreach ($this->costCalculators as $calculator) {
            if ($calculator->supports($definition, $level)) {
                return $calculator;
            }
        }

        return null;
    }

    private function getCostCacheKey(): string
    {

    }


}