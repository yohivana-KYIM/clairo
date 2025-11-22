<?php

namespace App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service;


use App\Service\Workflow\Classes\NotificationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Service\Storage\CloudStorageManager;
use App\AdminBundle\SubBundles\ExportingSubBundle\Application\Message\BulkExportMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class BulkExportHandler
{
    private ExportManager $exportManager;
    private CloudStorageManager $cloudStorageManager;
    private NotificationService $notificationService;
    private int $maxRecords;

    public function __construct(
        ExportManager $exportManager,
        CloudStorageManager $cloudStorageManager,
        NotificationService $notificationService,
        ParameterBagInterface $params
    ) {
        $this->exportManager = $exportManager;
        $this->cloudStorageManager = $cloudStorageManager;
        $this->notificationService = $notificationService;
        $this->maxRecords = $params->get('exporting_sub_bundle')['bulk']['max_records'] ?? 10000;
    }

    public function __invoke(BulkExportMessage $message): void
    {
        $dataChunks = array_chunk($message->getData(), $this->maxRecords);
        $fileUrls = [];

        foreach ($dataChunks as $index => $chunk) {
            $fileName = sprintf("exports/%s_%d.%s", $message->getEntityClass(), $index + 1, $message->getFormat());
            $exportResponse = $this->exportManager->export($message->getEntityClass(), $message->getFormat(), $chunk, $message->getCustomOptions());

            $this->cloudStorageManager->storeFile($fileName, $exportResponse->getContent());
            $fileUrls[] = $this->cloudStorageManager->getFileUrl($fileName);
        }

        // Notify user with download links
        $this->notificationService->sendAppNotification($message->getUser(), sprintf('Your bulk export is ready %s', implode(', ', $fileUrls)));
    }
}
