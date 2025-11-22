<?php

namespace App\Service\Factory;

use ReflectionClass;
use ReflectionException;

class BaseFileEntityFactory
{

    public function createFromFieldName(string $fieldClass, string $fieldName): ?object
    {
        try {
            $reflection = new ReflectionClass($fieldClass);
            $property = $reflection->getProperty($fieldName);
            $type = $property->getType();

            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                if (class_exists($className)) {
                    return new $className();
                }
            }
        } catch (ReflectionException $e) {
            return null;
        }

        return null;
    }
}
