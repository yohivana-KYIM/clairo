<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\Entity;

use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\Exportable;
use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\ExportField;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[Exportable(
    formats: ['csv', 'json'],
    template: "csv/product_export.csv.twig",
    encoding: "utf-8",
    variableDetection: ["prefix" => "[[", "suffix" => "]]", "variable_case" => "camel"],
    formatConfig: ["csv" => ["delimiter" => "|", "enclosure" => "'", "escape_char" => ""]]
)]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    #[ExportField(name: "Product ID")]
    private int $id;

    #[ORM\Column(type: TYPES::STRING)]
    #[ExportField(name: "Product Name")]
    private string $name;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[ExportField(name: "Price", format: "currency")]
    private float $price;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[ExportField(name: "Created At", format: "date:Y-m-d")]
    private \DateTimeInterface $createdAt;
}
