<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class ExportModelMapper
{
    /**
     * @throws ReflectionException
     */
    public function mapData(array $entities, string $modelClass): array
    {
        $mappedData = [];
        $reflection = new ReflectionClass($modelClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($entities as $entity) {
            $mappedItem = [];
            foreach ($properties as $property) {
                $mappedItem[$property->getName()] = $entity[$property->getName()] ?? null;
            }
            $mappedData[] = $mappedItem;
        }

        return $mappedData;
    }
}