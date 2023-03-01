<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

class IntegerToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        Assert::string($value);

        return (int) $value;
    }

    public function reverseTransform(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return (string) $value;
    }
}
