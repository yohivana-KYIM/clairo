<?php

namespace App\MultiStepBundle\Domain\Person;

use App\MultiStepBundle\Form\Person\PersonAccessStepThreeFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsTaggedItem(index: 3)]
#[AutoconfigureTag('person.multi_step.workflow_step')]
class PersonAccessStepThree extends AbstractPersonStep
{
    public function getId(): string
    {
        return self::STEP_PREFIX . 'step_three';
    }

    public function getDefaultIndex(): int
    {
        return 3;
    }

    public function getFormType(): string
    {
        return PersonAccessStepThreeFormType::class;
    }

    public function getName(): string
    {
        return 'Certifications et qualifications';
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/person_access_step_three_script.js';
    }
}