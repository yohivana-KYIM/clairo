<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Model;

use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\ExportField;

class ProductExportModel
{
    #[ExportField(name: "Product ID")]
    public int $id;

    #[ExportField(name: "Product Name")]
    public string $name;

    #[ExportField(name: "Price", format: "currency")]
    public float $price;

    #[ExportField(name: "Created At", format: "date:Y-m-d")]
    public string $createdAt;
}