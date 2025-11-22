<?php

namespace App\Twig;

use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('twig.extension')]
class MathExtraExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('max', [$this, 'maxFilter']),
            new TwigFilter('min', [$this, 'minFilter']),
        ];
    }

    public function getFunctions(): array
    {
        // alias en fonction (optionnel)
        return [
            new TwigFunction('max', [$this, 'maxFilter']),
            new TwigFunction('min', [$this, 'minFilter']),
        ];
    }

    /**
     * @param array|mixed $value
     * @return mixed|null
     */
    public function maxFilter(mixed $value): mixed
    {
        $arr = $this->toArray($value);
        if (empty($arr)) {
            return null;
        }
        $max = null;
        foreach ($arr as $v) {
            if ($v === null) { continue; }
            if ($max === null || $this->compare($v, $max) > 0) {
                $max = $v;
            }
        }
        return $max;
    }

    /**
     * @param array|mixed $value
     * @return mixed|null
     */
    public function minFilter(mixed $value): mixed
    {
        $arr = $this->toArray($value);
        if (empty($arr)) {
            return null;
        }
        $min = null;
        foreach ($arr as $v) {
            if ($v === null) { continue; }
            if ($min === null || $this->compare($v, $min) < 0) {
                $min = $v;
            }
        }
        return $min;
    }

    /** @return array<int,mixed> */
    private function toArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if ($value instanceof Traversable) {
            return iterator_to_array($value, false);
        }
        // scalaire : on traite comme un tableau à 1 élément
        return [$value];
    }

    /** Compare numériquement quand c'est possible, sinon lexicographiquement. */
    private function compare($a, $b): int
    {
        $aNum = is_int($a) || is_float($a) || (is_string($a) && is_numeric($a));
        $bNum = is_int($b) || is_float($b) || (is_string($b) && is_numeric($b));

        if ($aNum && $bNum) {
            return ($a + 0) <=> ($b + 0);
        }

        return (string) $a <=> (string) $b;
    }
}
