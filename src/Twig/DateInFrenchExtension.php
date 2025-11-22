<?php

namespace App\Twig;

use DateMalformedStringException;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('twig.extension')]
class DateInFrenchExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('date_in_french', [$this, 'formatDateInFrench']),
        ];
    }

    /**
     * @throws DateMalformedStringException
     */
    public function formatDateInFrench($date): string
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        if (!$date) {
            return '';
        }

        \Locale::setDefault('fr_FR');

        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::SHORT,
            $date->getTimezone(),
            \IntlDateFormatter::GREGORIAN,
            "d MMMM yyyy 'à' HH'h'mm"
        );

        return $formatter->format($date);
    }
}
