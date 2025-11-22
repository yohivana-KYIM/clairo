<?php

namespace App\Form;

use App\Entity\DocumentPersonnel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DocumentPersonnelType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('arrondissementNaissance', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('identity', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => true,
                    'class' => 'form-control is-invalid fileInput',
                ],
            ])
            ->add('Photo', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('Casier', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('acteNaiss', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('domicile', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('hebergement', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('IdentHebergent', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('sejour', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
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
            'data_class' => DocumentPersonnel::class,
        ]);
    }
}
