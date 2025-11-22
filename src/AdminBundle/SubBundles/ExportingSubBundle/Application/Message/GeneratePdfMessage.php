<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message;

use App\Entity\User;

class GeneratePdfMessage
{
    private string $entity;
    private array $data;
    private array $options;
    private User $user;

    public function __construct(string $entity, array $data, array $options, User $user)
    {
        $this->entity = $entity;
        $this->data = $data;
        $this->options = $options;
        $this->user = $user;
    }

    public function getEntity(): string {
		 return $this->entity;
	}
    public function getData(): array {
		 return $this->data;
	}
    public function getOptions(): array {
		 return $this->options;
	}
    public function getUser(): user {
		 return $this->user;
	}
}
