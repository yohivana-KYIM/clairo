<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Form\Vehicule\VehicleAccessStepFiveFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AutoconfigureTag('vehicule.multi_step.workflow_step')]
#[AsTaggedItem(index: 5)]
class VehicleAccessStepFive extends AbstractVehicleStep
{
    protected array $data = [];
    private string $fileTargetDirectory;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->fileTargetDirectory = $this->parameterBag->get('public_directory'). 'uploads/vehicles/'. $this->getId();
    }

    public function getId(): string
    {
        return 'vehicle_step_five';
    }

    public function getDefaultIndex(): int
    {
        return 5;
    }

    public function getFormType(): string
    {
        return VehicleAccessStepFiveFormType::class;
    }

    public function setMode(string $mode): void
    {

    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function validate(FormInterface $form): bool
    {
        return $form->isValid();
    }

    public function isCompleted(): bool
    {
        return !empty($this->data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getName(): string
    {
        return 'Pièces jointes';
    }

    public function getPersistenceStrategy(): string
    {
        return 'session';
    }

    public function process(FormInterface $form): void
    {
        $formData = $form->getData();

        foreach ($formData as $key => $value) {
            if ($value instanceof UploadedFile) {
                $this->handleFileUpload($key, $value, $formData);
            }
        }
        $this->data = $formData;
    }

    public function handleFileUpload(string $key, UploadedFile $file, array &$formData): void
    {
        // Custom logic for handling uploaded files (e.g., moving to a specific directory)
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->fileTargetDirectory, $fileName);

        // Update the data with the stored file path
        $formData[$key] = $this->fileTargetDirectory . '/' . $fileName;
    }

    public function processLoadedData(array $data): array
    {
        $filePathPattern = '/^\/srv\/app\/public\/uploads\/(vehicles|persons)\/.+\/.+.(pdf|jpg|jpeg|png)$/i';

        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match($filePathPattern, $value) && file_exists(str_replace($this->parameterBag->get('public_directory'), '', $value))) {
                $data[$key] = new UploadedFile($value, basename($value), mime_content_type($value), null, true);
            } else {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function checkStepDatas(array $data): array {

        $filePathPattern = '#^' . preg_quote($_ENV['APP_PUBLIC_PATH'] ?? '/srv/app/public', '#') . '/uploads/(vehicles|persons)/.+/.+\.(pdf|jpg|jpeg|png)$#i';

        $errors = [];
        foreach ($data as $key => $file) {
            if ($file) {
                if ($file instanceof UploadedFile) {
                    $this->validateUpload($file, $errors, $key);
                } else {
                    $errors[$key] = ['le fichier envoyé est invalide'];
                }
            }
        }

        foreach ($errors as $key => $value) {
            if(empty($value)) unset($errors[$key]);
        }

        return $errors;
    }

    public function validateUpload(UploadedFile $file, array &$errors, string $key): bool
    {
        $allowedMime = [
            'application/pdf',
            'image/jpeg','image/png','image/gif','image/bmp','image/webp',
            'image/tiff','image/heic','image/heif'
        ];

        $allowedExt = ['pdf','jpg','jpeg','jpe','png','gif','bmp','webp','tif','tiff','heic','heif'];
        $maxBytes   = 5 * 1024 * 1024; // 5 MB

        $errors[$key] = [];
        $path = $file->getRealPath();

        // =========================================================================
        // 0 • CORRUPTION BASIQUE
        // =========================================================================
        if (!$path || !is_readable($path)) {
            $errors[$key][] = "File unreadable or corrupted";
            return true;
        }

        if ($file->getSize() === 0) {
            $errors[$key][] = "File is empty (corrupted)";
            return true;
        }

        // =========================================================================
        // 1 • LIMITE DE TAILLE
        // =========================================================================
        if ($file->getSize() > $maxBytes) {
            $errors[$key][] = "File too large (>5MB)";
        }

        // =========================================================================
        // 2 • EXTENSION
        // =========================================================================
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExt, true)) {
            $errors[$key][] = "Extension not allowed ($ext)";
        }

        // =========================================================================
        // 3 • MIME RÉEL (FILEINFO)
        // =========================================================================
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($path);

        if (!in_array($realMime, $allowedMime, true)) {
            $errors[$key][] = "Real MIME type not allowed ($realMime)";
        }

        // =========================================================================
        // 4 • CORRUPTION AVANCÉE SELON LE TYPE
        // =========================================================================
        if (str_contains($realMime, 'image/')) {
            if (@getimagesize($path) === false) {
                $errors[$key][] = "Image file is corrupted or unreadable";
            }
        }

        if ($realMime === "application/pdf") {
            $fh = fopen($path, 'rb');
            $header = fread($fh, 5);
            fclose($fh);

            if ($header !== "%PDF-") {
                $errors[$key][] = "PDF header missing → corrupted or fake PDF";
            }
        }

        // =========================================================================
        // 5 • SCAN ANTIVIRUS CLAMAV
        // =========================================================================
        try {
            if ($this->scanWithClamAV($path) === false) {
                $errors[$key][] = "Potential malware detected";
            }
        } catch (\Throwable $e) {
            $errors[$key][] = "Antivirus unavailable";
        }

        // =========================================================================
        // 6 • SUPPRESSION EXIF DANGEREUX (XSS)
        // =========================================================================
        if (str_contains($realMime, 'image/jpeg')) {
            try {
                $this->cleanExif($path);
            } catch (\Throwable $e) {
                $errors[$key][] = "Failed to sanitize EXIF data";
            }
        }

        // =========================================================================
        return !empty($errors[$key]); // true = errors = invalid
    }

    private function cleanExif(string $path): void
    {
        if (!function_exists('exif_read_data')) return;

        $image = imagecreatefromjpeg($path);
        imagejpeg($image, $path, 100);
        imagedestroy($image);
    }

    private function scanWithClamAV(string $path): bool
    {
        $socket = fsockopen("clamav", 3310, $errno, $errstr, 2);
        if (!$socket) return true; // antivirus pas dispo → on laisse passer

        fwrite($socket, "zINSTREAM\0");

        $fh = fopen($path, "rb");
        while (!feof($fh)) {
            $chunk = fread($fh, 8192);
            $size = pack("N", strlen($chunk));
            fwrite($socket, $size . $chunk);
        }
        fwrite($socket, pack("N", 0)); // fin de flux

        $response = fgets($socket);
        fclose($fh);
        fclose($socket);

        return !str_contains($response, "FOUND");
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/vehicle_access_step_five_script.js';
    }
}
