<?php

namespace App\MultiStepBundle\Domain\Person;

use App\MultiStepBundle\Form\Person\PersonAccessStepOneFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AsTaggedItem(index: 1)]
#[AutoconfigureTag('person.multi_step.workflow_step')]
class PersonAccessStepOne extends AbstractPersonStep
{
    public function getId(): string
    {
        return self::STEP_PREFIX . 'step_one';
    }

    public function getDefaultIndex(): int
    {
        return 1;
    }

    public function getFormType(): string
    {
        return PersonAccessStepOneFormType::class;
    }

    public function getName(): string
    {
        return 'Informations sur la demande';
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/person_access_step_one_script.js';
    }
}