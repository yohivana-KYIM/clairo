<?php

namespace App\Form;

use App\Entity\EtatCivil;
use Assert\LessThanOrEqual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EtatCivilType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', ChoiceType::class, [
                'placeholder' => 'Choisissez votre titre',
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
                'choices' => [
                    'Monsieur' => 'monsieur',
                    'Madame' => 'madame',
                ],
            ])
            ->add('Nom', TextType::class, [
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
            ->add('prenom2', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('prenom3', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('prenom4', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
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
                    new \Symfony\Component\Validator\Constraints\LessThanOrEqual([
                        'value' => '-18 years',
                        'message' => 'Vous devez avoir au moins 18 ans.',
                    ]),
                ],
            ])
            ->add('paysNaissance', CountryType::class, [
                'placeholder' => 'Choisissez votre pays de naissance',
                'label' => false,
                'required' => true,
                'preferred_choices' => ['FR'],
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nom',
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('lieuNaissance', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('cpNaissance', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control is-invalid',
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{5}$/',
                        'message' => 'Veuillez entrer un code postal valide (5 chiffres).',
                    ]),
                ],
            ])
            ->add('arrondissementNaissance', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('nomMarital', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => false,
                    'class' => 'form-control',
                ],
            ])
            ->add('nationalite', ChoiceType::class, [
                'placeholder' => 'Choisissez votre nationalité',
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => false,
                ],
                'choices' => [
                    'Français' => 'francais',
                    'Étranger (documents à fournir)' => 'etranger',
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
            'data_class' => EtatCivil::class,
        ]);
    }
}
