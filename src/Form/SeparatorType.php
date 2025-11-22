<?php

namespace App\Form;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AutoconfigureTag('form.type')]
class SeparatorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Separator or subtitle logic (empty as it's just a visual element).
    }

    public function getBlockPrefix(): string
    {
        return 'separator';
    }
}
