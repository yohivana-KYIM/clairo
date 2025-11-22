<?php

namespace App\Form;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EntrepriseType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('codeAPE', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('signe', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('complementNom', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('tvaIntraCommunautaire', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('secteur', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('statut', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                ],
            ])
            ->add('type', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('nature', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
                'invalid_message' => 'Le numéro SIRET est invalide',
            ])
            ->add('numTelephone', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?(?:\d{1,4}[-\s])?\d{1,15}$/',
                        'message' => 'Le numéro de téléphone est invalide',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Numero Téléphone (ex : +33123456789)',
                ],
            ])
            ->add('nomResponsable', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('siren', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('naf', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
            ])
            ->add('nationalite', CountryType::class, [
                'placeholder' => 'Choisissez la nationalité de l\'entreprise',
                'label' => false,
                'required' => true,
                'preferred_choices' => ['FR'],
            ])
            ->add('emailReferent', EmailType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
                'invalid_message' => 'L\'adresse email est invalide',
            ])
            ->add('suppleant1', EmailType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
                'invalid_message' => 'L\'adresse email est invalide',
            ])
            ->add('suppleant2', EmailType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                ],
                'invalid_message' => 'L\'adresse email est invalide',
            ])
            ->add('gratuit', CheckboxType::class, [
                'required' => false,
                'label' => 'Cette entreprise est-elle gratuite ?',
            ])
            ->add('entrepriseMere', EntityType::class, options: [
                'class' => Entreprise::class,
                'choice_label' => 'nom',
                'label' => 'Entreprise mère',
                'label_attr' => ['class' => 'h5'],
                'required' => false,
                'placeholder' => 'Aucune (entreprise autonome)',
                'expanded' => false,
                'multiple' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $current = $options['data'] ?? null;
                    $qb = $er->createQueryBuilder('e')
                        ->orderBy('e.nom', 'ASC');
                    if ($current && $current->getId()) {
                        $qb->where('e != :current')
                        ->setParameter('current', $current);
                    }
                    return $qb;
                }
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
            'data_class' => Entreprise::class,
        ]);
    }
}
