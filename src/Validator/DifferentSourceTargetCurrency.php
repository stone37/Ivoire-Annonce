<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute] class DifferentSourceTargetCurrency  extends Constraint
{
    public string $message = 'La devise de référence et la devise cible doivent être différente.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}