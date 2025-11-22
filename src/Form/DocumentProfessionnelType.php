<?php

namespace App\Form;

use App\Entity\DocumentProfessionnel;
use Symfony\Component\Form\AbstractType;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DocumentProfessionnelType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateGies0Debut', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateGies0Fin', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateAtex0Debut', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateAtex0Fin', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('gies0', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('gies1', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control border border-dark-subtle',
                ],
            ])
            ->add('gies2', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control border border-dark-subtle',
                ],
            ])
            ->add('atex0', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('autre', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control border border-dark-subtle',
                ],
            ])
            ->add('date_gies1_fin', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('date_gies2_fin', DateType::class, [
                'widget' => 'single_text',
                'label' =>  false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
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
            'data_class' => DocumentProfessionnel::class,
        ]);
    }
}
