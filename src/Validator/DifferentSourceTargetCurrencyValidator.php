<?php

namespace App\Validator;

use App\Entity\ExchangeRate;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

class DifferentSourceTargetCurrencyValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var DifferentSourceTargetCurrency $constraint */
        Assert::isInstanceOf($constraint, DifferentSourceTargetCurrency::class);

        if (!$value instanceof ExchangeRate) {
            throw new UnexpectedTypeException($value, ExchangeRate::class);
        }

        if ($value->getSourceCurrency() === $value->getTargetCurrency()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
