<?php

namespace App\MultiStepBundle\Loader;

use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class WorkflowConfigLoader
{
    private array $config;

    public function __construct(string $configPath)
    {
        $locator = new FileLocator([$configPath]);
        $configFile = $locator->locate('multi_step.yaml');
        $this->config = Yaml::parseFile($configFile);
    }

    public function getWorkflowConfig(string $workflowName): array
    {
        if (!isset($this->config['multi_step']['workflows'][$workflowName])) {
            throw new InvalidArgumentException("Workflow configuration for '$workflowName' not found.");
        }

        return $this->config['multi_step']['workflows'][$workflowName];
    }

    public function getSteps(string $workflowName): array
    {
        $workflowConfig = $this->getWorkflowConfig($workflowName);

        if (!isset($workflowConfig['steps'])) {
            throw new InvalidArgumentException("Steps configuration for '$workflowName' not found.");
        }

        return $workflowConfig['steps'];
    }
}
