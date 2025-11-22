<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectLoggerListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[AsEventListener(event: 'kernel.response')]
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        // Check if the response is a redirect (status 3xx)
        if ($response instanceof RedirectResponse) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            $traceMessages = [];

            foreach ($backtrace as $trace) {
                $file = $trace['file'] ?? 'unknown file';
                $line = $trace['line'] ?? 'unknown line';
                $function = $trace['function'] ?? 'unknown function';

                $traceMessages[] = sprintf("File: %s, Line: %s, Function: %s", $file, $line, $function);
            }

            $this->logger->debug("Redirection detected. Backtrace:\n" . implode("\n", $traceMessages));
        }
    }
}
