<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Security;

use App\AdminBundle\SubBundles\ExportingSubBundle\Domain\Annotation\Exportable;
use ReflectionException;
use Symfony\Bundle\SecurityBundle\Security;
use ReflectionClass;

class ExportPermissionChecker
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @throws ReflectionException
     */
    public function isExportAllowed(string $entityClass): bool
    {
        $reflection = new ReflectionClass($entityClass);
        $attributes = $reflection->getAttributes(Exportable::class);
        $entityConfig = $attributes ? $attributes[0]->newInstance() : null;

        if (!$entityConfig) {
            return false;
        }

        foreach ($entityConfig->allowedRoles as $role) {
            if ($this->security->isGranted($role)) {
                return true;
            }
        }

        return false;
    }
}
