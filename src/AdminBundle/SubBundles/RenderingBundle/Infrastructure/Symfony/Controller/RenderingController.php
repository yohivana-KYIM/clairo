<?php

namespace App\AdminBundle\SubBundles\RenderingBundle\Infrastructure\Symfony\Controller;

use App\AdminBundle\RenderingBundle\Application\Service\RendererStrategyInterface;
use App\AdminBundle\SubBundles\RenderingBundle\Application\Service\RenderingManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RenderingController extends AbstractController
{
    public function __construct(
        private RenderingManager $renderingManager
    ) {}

    #[Route('/rendering', name: 'rendering_list')]
    public function list(Request $request): Response
    {
        // Select rendering strategy from query param or default to 'bootstrap'
        $strategy = $request->query->get('strategy', 'bootstrap');

        $data = [
            'title' => 'Rendering Demo',
            'items' => ['Item 1', 'Item 2', 'Item 3'],
        ];

        return match ($strategy) {
            'tailwind' => $this->getRenderingManager()->render($request, 'admin_bundle/rendering/tailwind/list.html.twig', $data),
            'api' => $this->getRenderingManager()->render($request, 'admin_bundle/rendering/api/list.json.twig', $data, new Response('', 200, ['Content-Type' => 'application/json'])),
            default => $this->getRenderingManager()->render($request, 'admin_bundle/rendering/bootstrap/list.html.twig', $data),
        };
    }

    public function getRenderingManager(): RenderingManager
    {
        return $this->renderingManager;
    }

    public function setRenderingManager(RenderingManager $renderingManager): void
    {
        $this->renderingManager = $renderingManager;
    }
}
