<?php

namespace App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Config;

class RenderingConfig
{
    private string $renderingStrategy;
    private array $defaultTemplates;

    public function __construct(array $config = [])
    {
        $this->renderingStrategy = $config['rendering_strategy'] ?? 'twig_bootstrap'; // Default to Twig-Bootstrap
        $this->defaultTemplates = $config['default_templates'] ?? [];
    }

    public function getRenderingStrategy(): string
    {
        return $this->renderingStrategy;
    }

    public function getDefaultTemplate(string $viewType): string
    {
        return $this->defaultTemplates[$viewType] ?? "$viewType.html.twig";
    }

    public function getDefaultTemplates(): array
    {
        return $this->defaultTemplates;
    }
}
