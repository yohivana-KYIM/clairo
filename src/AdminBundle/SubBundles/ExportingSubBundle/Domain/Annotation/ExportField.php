<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ExportField
{
    public function __construct(public string $name = '', public ?string $format = null)
    {
    }
}
