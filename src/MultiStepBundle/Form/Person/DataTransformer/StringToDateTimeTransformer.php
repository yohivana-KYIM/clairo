<?php

namespace App\MultiStepBundle\Form\Person\DataTransformer;

use DateTimeInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($value): ?\DateTimeInterface
    {
        // 1) Already a DateTime? return immediately.
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        // 2) Null or empty string → no value.
        if (null === $value || '' === $value) {
            return null;
        }

        // 3) Fast-path format detection by character positions:
        //    - ISO: “YYYY-MM-DD” has a dash at position 4
        //    - EU:  “DD/MM/YYYY” has a slash at position 2
        $format = null;
        if (isset($value[4]) && $value[4] === '-') {
            // quickly rule out strings that aren't exactly 10 chars
            if (strlen($value) === 10) {
                $format = 'Y-m-d';
            }
        } elseif (isset($value[2]) && $value[2] === '/') {
            if (strlen($value) === 10) {
                $format = 'd/m/Y';
            }
        }

        if (!$format) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid date format "%s". Expected “YYYY-MM-DD” or “DD/MM/YYYY”.',
                    $value
                )
            );
        }

        // 4) Single createFromFormat call and minimal validation
        $date = \DateTime::createFromFormat($format, $value);
        // on failure or mismatch, throw as before
        if (
            false === $date
            || $date->format($format) !== $value
        ) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid date "%s" for format %s.',
                    $value,
                    $format
                )
            );
        }

        return $date;
    }

    public function reverseTransform($value): ?string
    {
        // 1) If it’s already a DateTime, format directly:
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // 2) Null or empty → no value
        if (null === $value || '' === $value) {
            return null;
        }

        // 3) Fast‐path detect incoming string format:
        //    - ISO “YYYY-MM-DD” has a dash at position 4
        //    - EU  “DD/MM/YYYY” has a slash at position 2
        $format = null;
        if (isset($value[4]) && $value[4] === '-' && strlen($value) === 10) {
            $format = 'Y-m-d';
        } elseif (isset($value[2]) && $value[2] === '/' && strlen($value) === 10) {
            $format = 'd/m/Y';
        }

        if (!$format) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid date format "%s". Expected “YYYY-MM-DD” or “DD/MM/YYYY”.',
                    $value
                )
            );
        }

        // 4) Parse once and re‐format to our canonical view format:
        $date = \DateTime::createFromFormat($format, $value);
        if (
            false === $date
            || $date->format($format) !== $value
        ) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid date "%s" for format %s.',
                    $value,
                    $format
                )
            );
        }

        // 5) Return as “DD/MM/YYYY”
        return $date->format('Y-m-d');
    }
}
