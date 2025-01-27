<?php

namespace App\Service\ValueResolver;

use App\Modules\Core\DTO\PlanetDTO;
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
            || !is_subclass_of($argumentType, PlanetDTO::class, true)
        ) {
            return [];
        }

        // get the value from the request, based on the argument name
        $value = $request->attributes->get('planet');
        if (!is_string($value)) {
            return [];
        }

        // create and return the value object
        return [$value];
    }
}