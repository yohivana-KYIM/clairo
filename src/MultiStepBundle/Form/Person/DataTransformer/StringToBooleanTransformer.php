<?php

namespace App\MultiStepBundle\Form\Person\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StringToBooleanTransformer implements DataTransformerInterface
{
    public function transform($value): bool
    {
        return (bool) $value;
    }

    public function reverseTransform($value): bool
    {
        return (bool) $value;
    }
}