<?php

namespace App\Form;

use App\Entity\Filiation;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FiliationType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPere', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('prenomPere', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('nomMere', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('prenomMere', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
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
            'data_class' => Filiation::class,
        ]);
    }
}
