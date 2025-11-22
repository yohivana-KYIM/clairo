<?php

namespace App\AppIntegrationBundle;

use App\AppIntegrationBundle\Infrastructure\Symfony\DependencyInjection\AppIntegrationExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

class AppIntegrationBundle extends Bundle
{

    /**
     * @throws LoaderError
     */
    public function boot(): void
    {
        parent::boot();

        // Check if the service exists and is available
        if ($this->container->has(FilesystemLoader::class)) {
            /** @var FilesystemLoader $twigLoader */
            $twigLoader = $this->container->get(FilesystemLoader::class);
            $twigLoader->addPath(__DIR__ . '/Resources/views', 'AppIntegrationBundle');
        }
    }


    public function getContainerExtension(): ?ExtensionInterface
    {
        return new AppIntegrationExtension();
    }
}
