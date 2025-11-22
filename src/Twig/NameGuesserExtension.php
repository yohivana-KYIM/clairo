<?php

namespace App\Twig;

use App\Service\NameGuesser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Extension Twig permettant d’utiliser NameGuesser
 * directement dans les templates (filtres + fonctions).
 */
class NameGuesserExtension extends AbstractExtension
{
    private NameGuesser $nameGuesser;

    public function __construct(NameGuesser $nameGuesser)
    {
        $this->nameGuesser = $nameGuesser;
    }

    /**
     * Déclare les filtres utilisables dans Twig
     * Exemple : {{ email|guess_name }}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('guess_name', [$this->nameGuesser, 'guessName']),
            new TwigFilter('guess_first_name', [$this->nameGuesser, 'guessFirstName']),
            new TwigFilter('guess_last_name', [$this->nameGuesser, 'guessLastName']),
            new TwigFilter('guess_initials', [$this->nameGuesser, 'guessInitials']),
            new TwigFilter('guess_formal_name', [$this->nameGuesser, 'guessFormalName']),
            new TwigFilter('guess_domain', [$this->nameGuesser, 'guessDomain']),
        ];
    }

    /**
     * Déclare les fonctions utilisables dans Twig
     * Exemple : {{ guess_name(email) }}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('guess_name', [$this->nameGuesser, 'guessName']),
            new TwigFunction('guess_first_name', [$this->nameGuesser, 'guessFirstName']),
            new TwigFunction('guess_last_name', [$this->nameGuesser, 'guessLastName']),
            new TwigFunction('guess_initials', [$this->nameGuesser, 'guessInitials']),
            new TwigFunction('guess_formal_name', [$this->nameGuesser, 'guessFormalName']),
            new TwigFunction('guess_domain', [$this->nameGuesser, 'guessDomain']),
        ];
    }
}
