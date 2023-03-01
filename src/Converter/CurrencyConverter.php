<?php

namespace App\Converter;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;

class CurrencyConverter
{
    private ?array $cache = [];

    public function __construct(private ExchangeRateRepository $exchangeRateRepository)
    {
    }

    public function convert(int $value, string $sourceCurrencyCode, string $targetCurrencyCode): int
    {
        if ($sourceCurrencyCode === $targetCurrencyCode) {
            return $value;
        }

        $exchangeRate = $this->findExchangeRate($sourceCurrencyCode, $targetCurrencyCode);

        if (null === $exchangeRate) {
            return $value;
        }

        if ($exchangeRate->getSourceCurrency()->getCode() === $sourceCurrencyCode) {
            return (int) round($value * $exchangeRate->getRatio());
        }

        return (int) round($value / $exchangeRate->getRatio());
    }

    private function findExchangeRate(string $sourceCode, string $targetCode): ?ExchangeRate
    {
        $sourceTargetIndex = $this->createIndex($sourceCode, $targetCode);

        if (isset($this->cache[$sourceTargetIndex])) {
            return $this->cache[$sourceTargetIndex];
        }

        $targetSourceIndex = $this->createIndex($targetCode, $sourceCode);

        if (isset($this->cache[$targetSourceIndex])) {
            return $this->cache[$targetSourceIndex];
        }

        return $this->cache[$sourceTargetIndex] = $this->exchangeRateRepository->findOneWithCurrencyPair($sourceCode, $targetCode);
    }

    private function createIndex(string $prefix, string $suffix): string
    {
        return sprintf('%s-%s', $prefix, $suffix);
    }
}
