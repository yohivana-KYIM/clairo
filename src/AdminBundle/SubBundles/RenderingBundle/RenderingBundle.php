<?php

namespace App\AdminBundle\SubBundles\RenderingBundle;

use App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Symfony\DependencyInjection\RenderingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RenderingBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new RenderingExtension();
    }
}
