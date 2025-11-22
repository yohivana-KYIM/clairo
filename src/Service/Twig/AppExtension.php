<?php

namespace App\Service\Twig;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

#[AsTaggedItem(index: 'is_date')]
#[AutoconfigureTag('twig.extension')]
class AppExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest('date', function($v) {
                return $v instanceof \DateTimeInterface;
            }),
        ];
    }
}