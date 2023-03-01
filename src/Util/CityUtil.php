<?php

namespace App\Util;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\NonUniqueResultException;

class CityUtil
{
    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCity(string $name): ?City
    {
        return $this->cityRepository->getByName($name);
    }
}
