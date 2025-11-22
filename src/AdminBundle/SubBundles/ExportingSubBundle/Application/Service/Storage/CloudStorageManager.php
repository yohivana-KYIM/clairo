<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Storage;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CloudStorageManager
{
    private FilesystemOperator $storage;
    private array $config;

    public function __construct(#[Autowire(service: 'default.storage')]  FilesystemOperator $storage, ParameterBagInterface $params)
    {
        $this->storage = $storage;
        $this->config = $params->get('exporting_sub_bundle')['pdf']['storage'];
    }

    public function storeFile(string $path, string $content): void
    {
        $this->storage->write($path, $content);
    }

    public function getFileUrl(string $path): string
    {
        return sprintf("https://%s/%s/%s", $this->config['bucket'], $this->config['path'], $path);
    }
}
