<?php
namespace App\Security\FileSecurity\EventSubscribers;

use App\Security\FileSecurity\Events\FileUploadedEvent;
use App\Security\FileSecurity\Interfaces\FileSecurityCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FileSecuritySubscriber implements EventSubscriberInterface
{
    private $fileSecurityChecker;

    public function __construct(FileSecurityCheckerInterface $fileSecurityChecker)
    {
        $this->fileSecurityChecker = $fileSecurityChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FileUploadedEvent::NAME => 'onFileUploaded'
        ];
    }

    public function onFileUploaded(FileUploadedEvent $event): void
    {
        $file = $event->getFile();
        $this->fileSecurityChecker->validate($file);
    }
}
