<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute] class UniqueCurrencyPair extends Constraint
{
    public string $message = 'La paire de devises doit être unique.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
