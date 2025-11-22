<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\MessageHandler;


use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message\GeneratePdfMessage;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\ExportManager;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Storage\CloudStorageManager;
use App\Service\Workflow\Classes\NotificationService;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GeneratePdfHandler
{
    private ExportManager $exportManager;
    private CloudStorageManager $cloudStorageManager;
    private NotificationService $notificationService;

    public function __construct(
        ExportManager $exportManager,
        CloudStorageManager $cloudStorageManager,
        NotificationService $notificationService
    ) {
        $this->exportManager = $exportManager;
        $this->cloudStorageManager = $cloudStorageManager;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws Exception
     */
    public function __invoke(GeneratePdfMessage $message): void
    {
        $pdfResponse = $this->exportManager->export($message->getEntity(), 'pdf', $message->getData(), $message->getOptions());

        // Save the PDF file to cloud storage
        $filePath = 'exports/pdf/' . $message->getEntity() . '-' . time() . '.pdf';
        $this->cloudStorageManager->storeFile($filePath, $pdfResponse->getContent());

        // Notify user
        $this->notificationService->sendAppNotification($message->getUser(), "Your PDF export is ready {$this->cloudStorageManager->getFileUrl($filePath)}");
    }
}
