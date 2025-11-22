<?php

namespace App\MultiStepBundle\Form\Person;

use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Person\Rules\PersonAccessRulesStepFive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonAccessStepFiveFormType extends AbstractType
{
    use PersonAccessStepFormTrait;

    public function __construct(
        private readonly PersonAccessRulesStepFive $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section_mandatory', SeparatorType::class, [
                'label' => 'Pièces justificatives obligatoires',
            ])
            ->add('id_card', FileType::class, [
                'label' => 'Carte nationale d\'identité(recto et verso)',
                'attr' => [
                    'class' => 'or-required-idCardFile'
                ],
            ])
            ->add('passport', FileType::class, [
                'label' => 'Passeport',
                'attr' => [
                    'class' => 'or-required-idCardFile',
                ],
            ])
            ->add('residence_permit', FileType::class, [
                'label' => 'Carte ou  Titre de séjour(recto et verso)',
                'attr' => [
                    'class' => 'or-required-idCardFile',
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo d\'identité du demandeur(format jpeg, petite resolution)',
                'attr' => ['accept' => 'image/*'],
            ])
          /*  ->add('bank_receipt', FileType::class, [
                'label' => 'Justificatif bancaire',
            ])*/
            ->add('proof_of_address_host', FileType::class, [
                'label' => 'Justificatif de domicile (moins de 3 mois)',
            ])
            ->add('resident_id_card', FileType::class, [
                'label' => 'Carte d\'identité de l\'hébergeant(recto et verso)',
            ])
            ->add('section_optionnal', SeparatorType::class, [
                'label' => 'Pièces supplémentaires',
            ])
            ->add('resident_letter', FileType::class, [
                'label' => 'Attestation d\'hébergement(moins de 3 mois)',
            ])
            ->add('proof_of_address_hosted', FileType::class, [
                'label' => 'Justificatif de domicile de l\'hébergeant (moins de 3 mois)',
            ])
            ->add('zar_decision', FileType::class, [
                'label' => 'Décision préfectorale pour l\'accès Z.A.R.',
            ])
            ->add('doc_atex_0', FileType::class, [
                'label' => 'Attestion formation Atex',
            ])
            ->add('doc_gies_1', FileType::class, [
                'label' => 'Attestion formation Gies',
            ])
            /**->add('previous_card', FileType::class, [
                'label' => 'Ancien titre de circulation',
            ])*/
            ->add('loss_declaration', FileType::class, [
                'label' => 'Déclaration de perte/vol du titre de circulation',
            ])
            ->add('health_attestation', FileType::class, [
                'label' => 'Attestation de visite médicale',
            ])


            ->add('section_specific_cases', SeparatorType::class, [
                'label' => 'Documents spécifiques',
            ])
            ->add('taxi_card', FileType::class, [
                'label' => 'Carte professionnelle de taxi (uniquement pour les taxi)',
            ])
            ->add('birth_certificate', FileType::class, [
                'label' => 'Extrait d\'acte de naissance avec filiation(née à l\'étrangers)',
            ])
            ->add('criminal_record_origin', FileType::class, [
                'label' => 'Casier judiciaire du pays d\'origine',
            ])
            ->add('criminal_record_nationality', FileType::class, [
                'label' => 'Casier judiciaire du pays de la nationalité',
            ])
            ->add('criminal_record_resident_country', FileType::class, [
                'label' => 'Casier judiciaire du pays de résidence actuel.',
            ])
            ->add('refugee_attestation', FileType::class, [
                'label' => 'Attestation de l\'OFPRA',
            ])
            ->add('refugee_criminal_record', FileType::class, [
                'label' => 'Casier judiciaire national post-réfugié',
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
            'previous_step_data' => [],
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
