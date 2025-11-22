<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Exportable
{
    public function __construct(
        public array $formats = ['csv', 'json', 'xml', 'xlsx', 'pdf'],
        public ?string $template = null,
        public ?string $encoding = null,
        public ?array $variableDetection = null,
        public ?array $formatConfig = null,
        public array $allowedRoles = ['ROLE_EXPORT_SELECTED']
    ) {}
}