<?php

namespace App\CurveCalculator;

use Exception;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class CurveCalculatorProvider
{
    public function __construct(
        #[AutowireLocator(CurveCalculatorInterface::class)] private ServiceLocator $calculators
    ) {}

    /**
     * @param string $name
     * @return CurveCalculatorInterface
     */
    public function getCalculator(string $name): CurveCalculatorInterface
    {
        return $this->calculators->get($name);
    }

    /**
     * @return array<CurveCalculatorProvider>
     * @throws Exception
     */
    public function getAll(): array
    {
        return $this->calculators->getProvidedServices();
    }
}