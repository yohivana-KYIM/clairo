<?php

namespace App\AdminBundle\Domain\Event;

class AdminEvent
{
    public function __construct(private string $entityClass, private int $entityId) {}

    public function getEntityClass(): string {
		 return $this->entityClass;
	}
    public function getEntityId(): int {
		 return $this->entityId;
	}
}
