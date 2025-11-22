<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Domain\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Filterable
{
    public function __construct(public array $methods = [])
    {
    }
}
