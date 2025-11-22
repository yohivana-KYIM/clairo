<?php

namespace App\MultiStepBundle\Infrastructure\Symfony;

use Symfony\Contracts\EventDispatcher\Event;

class WorkflowMethodEvent extends Event
{
    public function __construct(
        private ?string $methodName = '',
        private ?array $params = [],
        private mixed $result = null
    ) {}

    public function getMethodName(): string
    {
        return $this->methodName;
    }
    public function setMethodName($methodName): void
    {
        $this->methodName = $methodName;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }

    public function setParams(?array $params): void
    {
        $this->params = $params;
    }
}
