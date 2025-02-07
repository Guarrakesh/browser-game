<?php

namespace App\Service\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class PlanetValueResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (
            !$argumentType
            || $argumentType !== 'int' || $argument->getName() !== 'planetId'
        ) {
            return [];
        }

        // get the value from the request, based on the argument name
        $value = $request->attributes->get('planetId');
        if (!is_string($value)) {
            return [];
        }

        // create and return the value object
        return [$value];
    }
}