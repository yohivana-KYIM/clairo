<?php

namespace App\MultiStepBundle\Domain\Person;


use App\MultiStepBundle\Form\Person\PersonAccessStepSixFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsTaggedItem(index: 6)]
#[AutoconfigureTag('person.multi_step.workflow_step')]
class PersonAccessStepSix extends AbstractPersonStep
{
    private string $fileTargetDirectory;

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->fileTargetDirectory = $this->parameterBag->get('public_directory'). 'uploads/persons/'. $this->getId();
    }
    public function getId(): string
    {
        return self::STEP_PREFIX . 'step_six';
    }

    public function getDefaultIndex(): int
    {
        return 6;
    }

    public function getFormType(): string
    {
        return PersonAccessStepSixFormType::class;
    }

    public function getName(): string
    {
        return 'Conditions générales';
    }

    public function process(FormInterface $form): void
    {
        $formData = $form->getData();
        $previousData = $this->getPreviousFormData();

        foreach ($formData as $key => $value) {
            if ($value instanceof UploadedFile) {
                $this->handleFileUpload($key, $value, $formData);
            } elseif ((is_null($value) || $value === '') && isset($previousData[$key])) {
                if ($previousData[$key] instanceof UploadedFile) {
                    $this->handleFileUpload($key, $previousData[$key], $formData, true);
                } else {
                    $formData[$key] = $previousData[$key];
                }
            }
        }
        $this->data = $formData;
    }

    public function handleFileUpload(string $key, UploadedFile $file, array &$formData, ?bool $reuse = true): void
    {
        if ($reuse) {
            $fileName = $file->getClientOriginalName();
        } else {
            $randomHex = substr(bin2hex(random_bytes(8)), 0, 16);

            $fileName = sprintf(
                '%s%s%s.%s',
                $key,
                date('Y_m_d_H-i-s'),
                $randomHex,
                $file->guessExtension()
            );
            $file->move($this->fileTargetDirectory, $fileName);
        }

        // Update the data with the stored file path
        $formData[$key] = $this->fileTargetDirectory . '/' . $fileName;
    }

    public function processLoadedData(array $data): array
    {
        $filePathPattern = '/^\/srv\/app\/public\/uploads\/(vehicles|persons)\/.+\/.+.(pdf|jpg|jpeg|png|svg)$/i';

        foreach ($data as $key => $value) {
            if (is_string($value) && preg_match($filePathPattern, $value) && file_exists(str_replace($this->parameterBag->get('public_directory'), '', $value))) {
                $data[$key] = new UploadedFile($value, basename($value), mime_content_type($value), null, true);
            }
        }

        return $data;
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/person_access_step_six_script.js';
    }
}
