<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

#[AsTaggedItem('twig.extension')]
class Base64Extension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('base64_decode', [$this, 'base64Decode']),
        ];
    }

    public function base64Decode(string $value): string
    {
        // 1. Supprimer toutes les balises HTML éventuelles
        $clean = strip_tags($value);

        // 2. Décoder le base64
        $decoded = base64_decode($clean, true);

        // 3. Gérer les erreurs si la chaîne n’est pas valide
        if ($decoded === false) {
            return $clean;
        }

        return strip_tags($decoded);
    }
}
