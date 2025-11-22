<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AdresseType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tourEtc', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('escalierEtc', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('numVoie', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('cp', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])

            ->add('distribution', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('pays', CountryType::class, [
                'placeholder' => 'Choisissez votre pays',
                'required' => true,
                'preferred_choices' => ['FR'],
                'label' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
        ;

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
            'data_class' => Adresse::class,
        ]);
    }
}
