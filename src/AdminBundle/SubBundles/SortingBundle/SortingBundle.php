<?php

namespace App\AdminBundle\SubBundles\SortingBundle;

use App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Symfony\DependencyInjection\SortingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SortingBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface {
        return new SortingExtension();
    }
}
