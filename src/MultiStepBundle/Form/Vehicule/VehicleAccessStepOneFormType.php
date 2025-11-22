<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\AppIntegrationBundle\Infrastructure\Symfony\Form\AutocompleteType;
use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Vehicule\Rules\VehicleAccessRulesStepOne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class VehicleAccessStepOneFormType extends AbstractType
{
    use VehicleAccessStepFormTrait;

    public function __construct(
        private readonly VehicleAccessRulesStepOne $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section_demandeur', SeparatorType::class, [
                'label' => 'Demandeur',
            ])
            ->add('enterpise_autocomplete', AutocompleteType::class, [
                'label' => 'Entreprise',
                'data_source_url' => '/api/autocomplete?query=:query&source=sirene',
                'data_keys' => [
                    'id'          => 'siret',
                    'label'       => 'uniteLegale.denominationUniteLegale',
                    'siren'       => 'siren',
                    'naf'         => 'uniteLegale.activitePrincipaleUniteLegale',
                    'address'     => 'adresseEtablissement.numeroVoieEtablissement_adresseEtablissement.typeVoieEtablissement_adresseEtablissement.libelleVoieEtablissement',  // you could also concatenate typeVoieEtablissement/libelleVoieEtablissement in your JS
                    'postal_code' => 'adresseEtablissement.codePostalEtablissement',
                    'city'        => 'adresseEtablissement.libelleCommuneEtablissement',
                    'country'     => '[[France]]',
                    'vat_number'     => 'calculateTVA(siren)',
                    'ape'     => 'formatAPE(uniteLegale.activitePrincipaleUniteLegale)'
                ],
                'data_targets' => [
                    'id'          => '#vehicle_access_step_one_form_siret_number',
                    'label'       => '#vehicle_access_step_one_form_company_name',
                    'siren'       => '#vehicle_access_step_one_form_siren_number',
                    'naf'         => '#vehicle_access_step_one_form_naf_number',
                    'ape'         => '#vehicle_access_step_one_form_ape_code',
                    'address'     => '#vehicle_access_step_one_form_address',
                    'postal_code' => '#vehicle_access_step_one_form_postal_code',
                    'city'        => '#vehicle_access_step_one_form_city',
                    'country'     => '#vehicle_access_step_one_form_country',
                    'vat_number'     => '#vehicle_access_step_one_form_vat_number',
                ],
            ])
            ->add('owner_or_renter', TextType::class, [
                'label' => 'Propriétaire ou locataire du véhicule',
            ])
            ->add('company_name', TextType::class, [
                'label' => 'Raison sociale',
            ])
            ->add('responsible_name', TextType::class, [
                'label' => 'Nom du responsable (Président, Directeur, etc.)',
            ])
            ->add('security_officer_email', TextType::class, [
                'label' => 'Email du référent sûreté',
            ])
            ->add('security_officer_phone', TextType::class, [
                'label' => 'Numéro de téléphone portable du référent sûreté',
            ])
            ->add('request_date', DateType::class, [
                'label' => 'Date de la demande',
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => [
                    'min'      => (new \DateTime())->format('Y-m-d'),
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value'   => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date doit être postérieure à aujourd’hui.',
                    ]),
                ],
            ])
            ->add('access_type', ChoiceType::class, [
                'label' => 'Type d\'accès',
                'choices' => [
                    'Première édition (création)' => 'creation',
                    'Renouvellement' => 'renewal',
                    'Duplicata suite à casse, perte ou vol' => 'duplicate',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('duplicate_reason', ChoiceType::class, [
                'label' => 'Motif du duplicata',
                'choices' => [
                    'Perte' => 'loss',
                    'Casse' => 'breaks',
                    'Vol' => 'theft',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('section_localisation', SeparatorType::class, [
                'label' => 'Localisation',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code Postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ])
            ->add('section_entreprise', SeparatorType::class, [
                'label' => 'Entreprise',
            ])
            ->add('siren_number', TextType::class, [
                'label' => 'N° SIREN',
            ])
            ->add('naf_number', TextType::class, [
                'label' => 'N° NAF',
            ])
            ->add('ape_code', TextType::class, [
                'label' => 'Code APE',
            ])
            ->add('siret_number', TextType::class, [
                'label' => 'N° SIRET',
            ])
            ->add('vat_number', TextType::class, [
                'label' => 'N° de TVA intracommunautaire',
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
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
            'required' =>  false
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
