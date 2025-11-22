<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Template;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VariableFormatter
{
    private string $prefix;
    private string $suffix;
    private string $variableCase;

    public function __construct(ParameterBagInterface $params)
    {
        $config = $params->get('exporting_sub_bundle')['variable_detection'];
        $this->prefix = $config['prefix'] ?? '{{';
        $this->suffix = $config['suffix'] ?? '}}';
        $this->variableCase = $config['variable_case'] ?? 'lower';
    }

    public function formatVariable(string $variable): string
    {
        return match ($this->variableCase) {
            'upper' => strtoupper($variable),
            'camel' => lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $variable)))),
            'snake' => strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($variable))),
            'kebab' => strtolower(preg_replace('/[A-Z]/', '-$0', lcfirst($variable))),
            default => strtolower($variable), // Default to lower case
        };
    }

    public function replaceVariables(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $formattedKey = $this->formatVariable($key);
            $placeholder = $this->prefix . $formattedKey . $this->suffix;
            $template = str_replace($placeholder, $value, $template);
        }
        return $template;
    }
}
