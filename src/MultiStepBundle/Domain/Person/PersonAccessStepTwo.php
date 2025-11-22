<?php

namespace App\MultiStepBundle\Domain\Person;

use App\MultiStepBundle\Form\Person\PersonAccessStepTwoFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsTaggedItem(index: 2)]
#[AutoconfigureTag('person.multi_step.workflow_step')]
class PersonAccessStepTwo extends AbstractPersonStep
{
    public function getId(): string
    {
        return self::STEP_PREFIX . 'step_two';
    }

    public function getDefaultIndex(): int
    {
        return 2;
    }

    public function getFormType(): string
    {
        return PersonAccessStepTwoFormType::class;
    }

    public function getName(): string
    {
        return 'informations sur l\'employé';
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/person_access_step_two_script.js';
    }
}