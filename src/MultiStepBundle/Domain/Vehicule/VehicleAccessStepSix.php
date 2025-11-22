<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Form\Vehicule\VehicleAccessStepSixFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AutoconfigureTag('vehicule.multi_step.workflow_step')]
#[AsTaggedItem(index: 6)]
class VehicleAccessStepSix extends AbstractVehicleStep
{
    protected array $data = [];

    private string $fileTargetDirectory;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->fileTargetDirectory = $this->parameterBag->get('public_directory'). 'uploads/vehicles/'. $this->getId();
    }

    public function getId(): string
    {
        return 'vehicle_step_six';
    }

    public function getDefaultIndex(): int
    {
        return 6;
    }

    public function getFormType(): string
    {
        return VehicleAccessStepSixFormType::class;
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
        return 'Engagement et pièces jointes';
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
        $filePathPattern = '/^\/srv\/app\/public\/uploads\/(vehicles|person)\/.+\/.+.(pdf|jpg|jpeg|png)$/i';

        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match($filePathPattern, $value) && file_exists(str_replace($this->parameterBag->get('public_directory'), '', $value))) {
                $data[$key] = new UploadedFile($value, basename($value), mime_content_type($value), null, true);
            }
        }
        $data['accept_terms'] = boolval($data['accept_terms'] ?? false);
        return $data;
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/vehicle_access_step_six_script.js';
    }
}