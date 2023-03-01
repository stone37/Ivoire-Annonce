<?php

namespace App\Controller\RequestDataHandler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdvertHybrideListDataHandler
{
    private ?string $typePropertyPrefix;
    private ?string $cityPropertyPrefix;
    private ?string $urgentPropertyPrefix;
    private ?string $dataPropertyPrefix;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->typePropertyPrefix = $parameterBag->get('app_type_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
        $this->urgentPropertyPrefix = $parameterBag->get('app_urgent_property_prefix');
        $this->dataPropertyPrefix = $parameterBag->get('app_data_property_prefix');
    }

    public function retrieveData(array $requestData): array
    {
        $data[$this->typePropertyPrefix] = $requestData[$this->typePropertyPrefix];
        $data[$this->cityPropertyPrefix] = (string) $requestData[$this->cityPropertyPrefix];
        $data[$this->urgentPropertyPrefix] = (bool) $requestData[$this->urgentPropertyPrefix];
        $data = array_merge($data, $requestData['price']);

        $this->handleDataPrefixedProperty($requestData, $data);

        return $data;
    }

    private function handleDataPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->dataPropertyPrefix])) {
            return;
        }

        $data[$this->dataPropertyPrefix] = (string) $requestData[$this->dataPropertyPrefix];
    }
}
