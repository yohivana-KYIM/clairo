<?php

namespace App\AdminBundle;

use App\AdminBundle\Infrastructure\Symfony\DependencyInjection\AdminExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdminBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface {
        return new AdminExtension();
    }
}