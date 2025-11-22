<?php

namespace App\Form;

use App\Entity\AdresseFacturation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdresseFacturationType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('codePostal', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('pays', CountryType::class, [
                'placeholder' => 'Choisissez le pays de votre entreprise',
                'label' => false,
                'required' => false,
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
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
            'data_class' => AdresseFacturation::class,
        ]);
    }
}
