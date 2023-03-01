<?php

namespace App\PropertyNameResolver;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SearchPropertyNameResolverRegistry
{
    private array $propertyNameResolvers = [];

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->propertyNameResolvers[] = [
            $parameterBag->get('app_name_property_prefix'),
            $parameterBag->get('app_description_property_prefix')
        ];
    }

    public function getPropertyNameResolvers(): array
    {
        return $this->propertyNameResolvers;
    }
}