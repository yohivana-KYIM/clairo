<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

#[AutoconfigureTag('twig.extension')]

class TypeTestsExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('string', 'is_string'),
            new TwigTest('scalar', 'is_scalar'),
            new TwigTest('numeric', 'is_numeric'),
        ];
    }
}
