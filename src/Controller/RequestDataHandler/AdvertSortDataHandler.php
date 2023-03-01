<?php

namespace App\Controller\RequestDataHandler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use UnexpectedValueException;

class AdvertSortDataHandler
{
    public const ORDER_BY_INDEX = 'order_by';
    public const SORT_INDEX = 'sort_by';
    public const SORT_ASC_INDEX = 'asc';
    public const SORT_DESC_INDEX = 'desc';

    private ?string $positionPropertyPrefix;
    private ?string $validateAtProperty;
    private ?string $priceProperty;
    private ?string $autoYearProperty;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->positionPropertyPrefix = $parameterBag->get('app_position_property_prefix');
        $this->validateAtProperty = $parameterBag->get('app_validated_at_property_prefix');
        $this->priceProperty = $parameterBag->get('app_price_property_prefix');
        $this->autoYearProperty = $parameterBag->get('app_auto_year_property_prefix');
    }

    public function retrieveData(array $requestData): array
    {
        $data = [];
        $positionSortingProperty = $this->positionPropertyPrefix;

        $orderBy = $requestData[self::ORDER_BY_INDEX] ?? $positionSortingProperty;
        $sort = $requestData[self::SORT_INDEX] ?? self::SORT_DESC_INDEX;

        $availableSorters = [
            $positionSortingProperty,
            $this->validateAtProperty,
            $this->priceProperty,
            $this->autoYearProperty
        ];

        $availableSorting = [self::SORT_ASC_INDEX, self::SORT_DESC_INDEX];

        if (!in_array($orderBy, $availableSorters) || !in_array($sort, $availableSorting)) {
            throw new UnexpectedValueException();
        }

        $orderBy = 'a.' . $orderBy;

        $data['sort_by'] = [$orderBy => $sort];

        return $data;
    }
}