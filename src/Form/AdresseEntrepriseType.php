<?php

namespace App\Form;

use App\Entity\AdresseEntreprise;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdresseEntrepriseType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numVoie', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('distribution', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('tourEtc', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('cp', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('numTelephone', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('adresseExistante', EntityType::class, [
                'class' => AdresseEntreprise::class,
                'label' => 'Sélectionner une adresse existante :',
                'required' => false,
                'placeholder' => 'Sélectionner une adresse',
                'mapped' => false,
                'choice_label' => function (AdresseEntreprise $adresse) {
                    return sprintf('%s %s %s (%s)', $adresse->getNumVoie(), $adresse->getDistribution(), $adresse->getVille(), $adresse->getCp());
                },
                'attr' => [
                    'class' => 'adresse-autocomplete',
                    'data-target-numvoie' => 'numVoie',
                    'data-target-distribution' => 'distribution',
                    'data-target-ville' => 'ville',
                    'data-target-cp' => 'cp',
                    'data-target-numtel' => 'numTelephone',
                ],
                'choice_attr' => function (AdresseEntreprise $adresse) {
                    return [
                        'data-numvoie' => $adresse->getNumVoie(),
                        'data-distribution' => $adresse->getDistribution(),
                        'data-ville' => $adresse->getVille(),
                        'data-cp' => $adresse->getCp(),
                        'data-numtel' => $adresse->getNumTelephone(),
                    ];
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.ville', 'ASC');
                },
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
            'data_class' => AdresseEntreprise::class,
        ]);
    }
}
