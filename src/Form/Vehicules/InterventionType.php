<?php

namespace App\Form\Vehicules;

use App\Entity\Entreprise;
use App\Entity\Intervention;
use App\Service\Validator\Constraint\DateGreaterThanToday;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class InterventionType extends AbstractType
{
    public function __construct(
        private readonly HtmlSanitizerInterface $sanitizer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exploitationFos', CheckboxType::class, [
                'label' => 'Exploitation Fos',
                'required' => false,
            ])
            ->add('exploitationLavera', CheckboxType::class, [
                'label' => 'Exploitation Lavera',
                'required' => false,
            ])
            ->add('motif', ChoiceType::class, [
                'placeholder' => 'Sélectionnez le motif de votre intervention',
                'label' => 'Motif de l\'intervention',
                'label_attr' => ['class' => 'h5'],
                'attr' => [
                    'class' => 'is-invalid',
                ],
                'choices' => [
                    'Livraison' => 'livraison',
                    'Visite' => 'visite',
                    'Personnel Fluxel' => 'Personnel Fluxel',
                    'Opération interne sous-traitant de Fluxel' => 'operation_interne_sous_traitant_de_fluxel',
                    'Opération interne client de Fluxel' => 'operation_interne_client_de_fluxel',
                    'Intervention pour un navire' => 'intervention_pour_un_navire',
                    'Autre' => 'autre',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un motif',
                    ]),
                ]])
            ->add('autre', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows' => 5,
                    'cols' => 40,
                ],
            ])
            ->add('duree', ChoiceType::class, [
                'placeholder' => 'Sélectionnez la durée de l\'intervention',
                'label' => 'Durée de l\'intervention',
                'label_attr' => ['class' => 'h5'],
                'choices' => [
                    'Permanent' => 'permanent',
                    'Temporaire' => 'temporaire',
                ],
            ])
            ->add('dateIntervention', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new DateGreaterThanToday(),
                ],
            ]);
            $builder
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'placeholder' => 'Sélectionnez votre entreprise',
                'choice_label' => 'nom',
                'data' => $options['entrepriseStockee'],
                'choices' =>  $options['entreprises'],
                'mapped' => false,
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
            'data_class' => Intervention::class,
            'entreprises' => [],
            'entrepriseStockee' => null,
        ]);
    }
}
