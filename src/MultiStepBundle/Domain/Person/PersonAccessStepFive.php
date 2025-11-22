<?php

namespace App\MultiStepBundle\Domain\Person;

use App\MultiStepBundle\Form\Person\PersonAccessStepFiveFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsTaggedItem(index: 5)]
#[AutoconfigureTag('person.multi_step.workflow_step')]
class PersonAccessStepFive extends AbstractPersonStep
{
    private string $fileTargetDirectory;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->fileTargetDirectory = $this->parameterBag->get('public_directory'). 'uploads/persons/'. $this->getId();
    }
    public function getId(): string
    {
        return self::STEP_PREFIX . 'step_five';
    }

    public function getDefaultIndex(): int
    {
        return 5;
    }

    public function getFormType(): string
    {
        return PersonAccessStepFiveFormType::class;
    }

    public function getName(): string
    {
        return 'Documents d\'identité';
    }

    public function process(FormInterface $form): void
    {
        $formData = $form->getData();
        $previousData = $this->getPreviousFormData();

        foreach ($formData as $key => $value) {
            if ($value instanceof UploadedFile) {
                $this->handleFileUpload($key, $value, $formData);
            } elseif ((is_null($value) || $value === '') && isset($previousData[$key])) {
                // Pas de nouvelle valeur : on garde l’ancienne (utile pour fichiers non resoumis)
                $formData[$key] = $previousData[$key];
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
        $filePathPattern = '#^' . preg_quote($_ENV['APP_PUBLIC_PATH'] ?? '/srv/app/public', '#') . '/uploads/(vehicles|persons)/.+/.+\.(pdf|jpg|jpeg|png)$#i';


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

    function validateUpload(UploadedFile $file, &$errors, $key): bool|string {
        $allowedMime = [
            'application/pdf',
            'image/jpeg','image/png','image/gif','image/bmp','image/webp',
            'image/tiff','image/heic','image/heif'
        ];
        $allowedExt = ['pdf','jpg','jpeg','jpe','png','gif','bmp','webp','tif','tiff','heic','heif'];
        $maxBytes   = 5 * 1024 * 1024; // 5 MB

        $errors[$key] = [];
        // 1. Size
        if ($file->getSize() > $maxBytes) {
            $errors[$key][] =  "File too large (>5MB)";
        }

        // 2. Extension
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExt, true)) {
            $errors[$key][] =  "Extension not allowed";
        }

        // 3. MIME from real file contents
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        if (!in_array($realMime, $allowedMime, true)) {
            $errors[$key][] = "Real MIME type not allowed ($realMime)";
        }

        if (empty($errors[$key])) return false;

        return true; // ✅ safe
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/person_access_step_five_script.js';
    }
}