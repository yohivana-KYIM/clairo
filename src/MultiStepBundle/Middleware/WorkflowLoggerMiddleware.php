<?php

namespace App\MultiStepBundle\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowLoggerMiddleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logRequest(Request $request): void
    {
        $this->logger->info('Workflow Request', [
            'path' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'params' => $request->request->all(),
        ]);
    }

    public function logResponse(Response $response): void
    {
        $this->logger->info('Workflow Response', [
            'status_code' => $response->getStatusCode(),
            'content' => $response->getContent(),
        ]);
    }

    public function logTransition(string $stepFrom, string $stepTo): void
    {
        $this->logger->info('Workflow Step Transition', [
            'from' => $stepFrom,
            'to' => $stepTo,
        ]);
    }

    public function logError(\Throwable $exception): void
    {
        $this->logger->error('Workflow Error', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}