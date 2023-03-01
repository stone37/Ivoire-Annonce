<?php

namespace App\Validator;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

class UniqueCurrencyPairValidator extends ConstraintValidator
{
    private ExchangeRateRepository $exchangeRateRepository;

    public function __construct(ExchangeRateRepository $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var UniqueCurrencyPair $constraint */
        Assert::isInstanceOf($constraint, UniqueCurrencyPair::class);

        if (!$value instanceof ExchangeRate) {
            throw new UnexpectedTypeException($value, ExchangeRate::class);
        }

        if (null !== $value->getId()) {
            return;
        }

        if (null === $value->getSourceCurrency() || null === $value->getTargetCurrency()) {
            return;
        }

        if (!$this->isCurrencyPairUnique($value->getSourceCurrency(), $value->getTargetCurrency())) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    private function isCurrencyPairUnique(Currency $baseCurrency, Currency $targetCurrency): bool
    {
        $exchangeRate = $this->exchangeRateRepository->findOneWithCurrencyPair($baseCurrency->getCode(), $targetCurrency->getCode());

        return null === $exchangeRate;
    }
}
