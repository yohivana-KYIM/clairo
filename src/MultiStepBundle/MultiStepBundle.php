<?php

namespace App\MultiStepBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Loader\FilesystemLoader;

class MultiStepBundle extends Bundle
{

    public function boot(): void
    {
        parent::boot();

        // Check if the service exists and is available
        if ($this->container->has(FilesystemLoader::class)) {
            /** @var FilesystemLoader $twigLoader */
            $twigLoader = $this->container->get(FilesystemLoader::class);
            $twigLoader->addPath(__DIR__ . '/Resources/views', 'MultiStepBundle');
        }
    }
}
