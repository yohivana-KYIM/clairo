<?php

namespace App\Form;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use App\Entity\ProblemeCarte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProblemeCarteType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('motif', ChoiceType::class, [
                'placeholder' => 'Veuillez choisir un motif',
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
                'choices' => [
                    'J\'ai perdu mon badge' => 'J\'ai perdu mon badge',
                    'On ma volé mon badge' => 'On ma volé mon badge',
                    'Mon badge ne fonctionne plus' => 'Mon badge ne fonctionne plus',
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control is-invalid',
                ],
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => '-18 years',
                        'message' => 'Vous devez avoir au moins 18 ans.',
                    ]),
                ],
            ])
            ->add('suiteDonner', ChoiceType::class, [
                'placeholder' => 'Veuillez sélectionner l\'un des deux choix',
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
                'choices' => [
                    'Demande de réédition' => 'Demande de réédition',
                    'Demande de suppression définitive' => 'Demande de suppression définitive',
                ],
            ])
            ->add('email', EmailType::class)
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
            'data_class' => ProblemeCarte::class,
        ]);
    }
}
