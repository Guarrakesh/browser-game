<?php

namespace App\Modules\Construction\Controller;


use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('controller.service_arguments')]
interface BuildingActionInterface
{
    public static function getName(): string;

}