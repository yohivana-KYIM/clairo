<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle;

use App\AdminBundle\SubBundles\ExportingSubBundle\Infrastructure\Symfony\DependencyInjection\ExportExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExportingSubBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface {
        return new ExportExtension();
    }
}