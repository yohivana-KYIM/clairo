<?php

namespace App\AdminBundle\SubBundles\PaginationBundle;

use App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Symfony\DependencyInjection\PaginationExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PaginationBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface {
        return new PaginationExtension();
    }
}
