<?php

namespace App\AdminBundle\SubBundles\RenderingBundle\Application\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Config\RenderingConfig;

class RenderingManager
{
    private string $strategy;
    private array $defaultTemplates;
    private Environment $twig;

    public function __construct(RenderingConfig $config, Environment $twig)
    {
        $this->strategy = $config->getRenderingStrategy();
        $this->defaultTemplates = $config->getDefaultTemplates();
        $this->twig = $twig;
    }

    public function render(Request $request, string $viewType, array $data): Response
    {
        if ($this->strategy === 'api' || $request->isXmlHttpRequest()) {
            return new JsonResponse($data);
        }

        $templateDir = match ($this->strategy) {
            'twig_tailwind' => '@Rendering/tailwind/',
            default => '@Rendering/bootstrap/',
        };

        $template = $templateDir . ($this->defaultTemplates[$viewType] ?? "$viewType.html.twig");

        return new Response($this->twig->render($template, $data));
    }
}
