<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\MultiStepBundle\Domain\Vehicule\Rules\VehicleAccessRulesStepOne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleAccessStepThreeFormType extends AbstractType
{

    public function __construct(
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fos_port_access', CheckboxType::class, [
                'label' => 'Port pétrolier de Fos-sur-Mer',
                'required' => false,
            ])
            ->add('lavera_port_access', CheckboxType::class, [
                'label' => 'Port pétrolier de Lavéra',
                'required' => false,
            ])
            ->add('fos_access_reason', TextareaType::class, [
                'label' => 'Motif de l’accès à Fos',
                'required' => false,
            ])
            ->add('lavera_access_reason', TextareaType::class, [
                'label' => 'Motif de l’accès à Lavéra',
                'required' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = $this->sanitizer->sanitize($value);
                }
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required' =>  false,
        ]);
    }
}
