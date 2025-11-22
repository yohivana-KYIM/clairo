<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\AppIntegrationBundle\Infrastructure\Symfony\Form\AutocompleteType;
use App\MultiStepBundle\Domain\Vehicule\Rules\VehicleAccessRulesStepTwo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleAccessStepTwoFormType extends AbstractType
{
    use VehicleAccessStepFormTrait;

    public function __construct(
        private readonly VehicleAccessRulesStepTwo $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // champ d’autocomplete pour la plaque
            ->add('registration_number', AutocompleteType::class, [
                'label'             => 'Numéro d’immatriculation',
                'attr'              => [
                    'placeholder' => 'AA-123-BC',
                    'class' => 'api-js-autocomplete',
                    'autocomplete' => 'off',
                ],
                'data_source_url'   => '/api/autocomplete?query=:query&source=vehicle_info_autocomplete',
                'mapped' => true,
                'data_keys' => [
                    // clefs issues de votre adapter (voir meta/value)
                    'label'                    => 'label',
                    'model'                    => 'modele',
                    'brand'                    => 'brand',
                    'immatriculation'          => 'immatriculation',
                    'firstRegistrationDate'    => 'meta.firstRegistrationDate',
                    'companyVehicle'           => 'meta.companyVehicle',
                    'giesType'                 => 'meta.typeCertificationGIES',
                    'giesExpiry'               => 'meta.giesExpiry',
                ],
                'data_targets' => [
                    // mapping JS: cible jQuery / querySelector pour chaque champ
                    'brand'                    => '#vehicle_access_step_two_form_brand',
                    'model'                    => '#vehicle_access_step_two_form_model',
                    'firstRegistrationDate'    => '#vehicle_access_step_two_form_first_registration_date',
                    'companyVehicle'           => 'input[name="vehicle_access_step_two_form[vehicle_type]"]',
                    'giesType'                 => 'input[name="vehicle_access_step_two_form[certification_type]"]',
                    'giesExpiry'               => '#vehicle_access_step_two_form_gies_expiry_date',
                ],
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle',
            ])
            ->add('first_registration_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de 1ère mise en circulation',
            ])
            ->add('vehicle_type', ChoiceType::class, [
                'choices' => [
                    'Véhicule d’entreprise' => 'company',
                    'Véhicule personnel' => 'personal',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Véhicule bénéficiant de la demande',
            ])
            ->add('certification_type', ChoiceType::class, [
                'choices' => [
                    'Véhicule non GIES' => 'non_gies',
                    'Véhicule GIES' => 'gies',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type de certification',
            ])
            ->add('gies_expiry_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de validité GIES',
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
            'required' =>  false,
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
