<?php
namespace App\Security\FileSecurity\Events;

use Symfony\Contracts\EventDispatcher\Event;

class FileUploadedEvent extends Event
{
    public const NAME = 'file.uploaded';
    private $file;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    public function getFile(): array
    {
        return $this->file;
    }
}
