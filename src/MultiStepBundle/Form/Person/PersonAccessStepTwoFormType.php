<?php

namespace App\MultiStepBundle\Form\Person;

use App\AppIntegrationBundle\Infrastructure\Symfony\Form\AutocompleteType;
use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Person\Rules\PersonAccessRulesStepTwo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonAccessStepTwoFormType extends AbstractType
{
    use PersonAccessStepFormTrait;

    public function __construct(
        private readonly PersonAccessRulesStepTwo $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section_employee_infos', SeparatorType::class, [
                'label' => 'Identité de l’employé',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Madame' => 'mme',
                    'Monsieur' => 'm',
                    'Autre' => 'xy',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('cni_type', ChoiceType::class, [
                'label' => 'Type de pièce d\'identité *',
                'choices' => [
                    'Passeport' => 'passeport',
                    'Carte nationale d\'identité' => 'cni',
                    'Carte de séjour' => 'sejour',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
            ])
            ->add('numero_cni', TextType::class, [
                'label' => 'Numéro de CNI/Passeport',
            ])
            ->add('employee_first_name', TextType::class, [
                'label' => 'Nom de l\'employé',
            ])
            ->add('employee_last_name', TextType::class, [
                'label' => 'Prenom de l\'employé',
            ])
            ->add('employee_last_name_2', TextType::class, [
                'label' => 'Prenom 2 de l\'employé',
            ])
            ->add('employee_last_name_3', TextType::class, [
                'label' => 'Prenom 3 de l\'employé',
            ])
            ->add('employee_last_name_4', TextType::class, [
                'label' => 'Prenom 4 de l\'employé',
            ])
            ->add('maiden_name', TextType::class, [
                'label' => 'Nom de jeune fille',
            ])
            ->add('employee_birthdate', DateType::class, [
                'label' => 'Date de naissance',
            ])
            ->add('employee_birth_postale_code', TextType::class, [
                'label' => 'Code postale naissance',
            ])
            ->add('employee_birthplace', TextType::class, [
                'label' => 'Lieu de naissance',
            ])
            ->add('employee_birth_district', TextType::class, [
                'label' => 'Arrondissement(si lieu de naissance dans paris, lyon, marseille)',
            ])
            ->add('nationality', AutocompleteType::class, [
                'data_source_url' => '/api/autocomplete?query=:query&source=nationality',
                'label' => 'Nationalité',
                'mapped' => true
            ])
            ->add('social_security_number', TextType::class, [
                'label' => 'Numéro de sécurité sociale',
            ])
            ->add('employee_email', TextType::class, [
                'label' => 'Email personnel',
            ])
            ->add('employee_phone', TextType::class, [
                'label' => 'Numéro de portable personnel',
            ])
            ->add('employee_refugee', ChoiceType::class, [
                'label' => 'Cochez la case si vous êtes refugié',
                'choices' => [
                    'Refugié' => 'refugee',
                ],
                'multiple' => true,
                'expanded' => true,
            ])

            ->add('section_address', SeparatorType::class, [
                'label' => 'Adresse',
            ])
            ->add('section_employee_address',AutocompleteType::class, [
                'label' => 'Adresse de l\'employé',
                'data_source_url' => '/api/autocomplete?query=:query&source=google',
                'data_keys' => [
                    'address'     => 'description',
                    'postal_code' => 'codePostale',
                    'city'        => 'ville',
                    'country'     => 'Pays'
                ],
                'data_targets' => [
                    'address'     => '#person_access_step_two_form_section_employee_address',
                    'postal_code' => '#person_access_step_two_form_postal_code',
                    'city'        => '#person_access_step_two_form_city',
                    'country'     => '#person_access_step_two_form_country',
                ],
                'mapped' => true
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ])
            ->add('resident_situation', ChoiceType::class, [
                'label' => 'Situation de logement',
                'choices' => [
                    'Je suis propriétaire ou locataire' => 'owner_or_tenant',
                    'Je suis hébergé' => 'hosted',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('section_parents', SeparatorType::class, [
                'label' => 'Parents',
            ])
            ->add('father_name', TextType::class, [
                'label' => 'Nom du père',
                'required' => true,
            ])
            ->add('father_first_name', TextType::class, [
                'label' => 'Prénom du père',
                'required' => true,
            ])
            ->add('mother_maiden_name', TextType::class, [
                'label' => 'Nom de jeune fille de la mère',
                'required' => true,
            ])
            ->add('mother_first_name', TextType::class, [
                'label' => 'Prénom de la mère',
                'required' => true,
            ])
            ->add('section_contract', SeparatorType::class, [
                'label' => 'Contrat',
            ])
            ->add('contract_type', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => 'cdi',
                    'CDD' => 'cdd',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('employee_function', TextType::class, [
                'label' => 'Fonction de l\'employé',
            ])
            ->add('employment_date', DateType::class, [
                'label' => 'Date d\'embauche',
                'widget' => 'single_text',
            ])
            ->add('contract_end_date', DateType::class, [
                'label' => 'Date de fin du contrat',
                'widget' => 'single_text',
            ]);

        $this->updateBuilderAddViewTransformer($builder);
        $this->addDynamicFieldListener($builder, $options);

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
            'data_class' => null,
            'required' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getRules(): object
    {
        return $this->rules;
    }
}
