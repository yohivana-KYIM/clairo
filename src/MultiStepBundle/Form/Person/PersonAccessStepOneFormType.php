<?php
namespace App\MultiStepBundle\Form\Person;

use App\AppIntegrationBundle\Infrastructure\Symfony\Form\AutocompleteType;
use App\Entity\User;
use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Person\Rules\PersonAccessRulesStepOne;
use App\Service\NameGuesser;
use Symfony\Bundle\SecurityBundle\Security;
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

class PersonAccessStepOneFormType extends AbstractType
{
    use PersonAccessStepFormTrait;

    public function __construct(
        private readonly PersonAccessRulesStepOne $rules,
        private readonly Security $security, private readonly NameGuesser $nameGuesser,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $entreprise = $user?->getEntreprise();

        $defaultData = [
            'company_name' => $entreprise?->getNom(),
            'siren' => $entreprise?->getSiren(),
            'naf' => $entreprise?->getNaf(),
            'siret' => $entreprise?->getSiret(),
            'vat_number' => $entreprise?->getTvaIntraCommunautaire(),
            'address' => $entreprise?->getAdresse()?->getAdresseComplete(),
            'postal_code' => $entreprise?->getAdresse()?->getCp(),
            'city' => $entreprise?->getAdresse()?->getVille(),
            'country' => $entreprise?->getAdresse()?->getPays() ?? 'France',
            'security_officer_email' => $entreprise?->getEmailReferent() ?? '',
            'alternate_referent_email' => $entreprise?->getSuppleant1() ?? $entreprise?->getSuppleant2() ?? '',
            'alternate_referent_phone' => $entreprise?->getTelephoneSuppleant1() ?? $entreprise?->getTelephoneSuppleant2(),
            'security_officer_phone' => $entreprise?->getTelephoneReferent(),
            'security_officer_position' => $entreprise ? 'Référent sûreté': null,
            'alternate_referent_position' => $entreprise ? 'Référent sûreté suppléant' : null,
        ];

        $builder
            ->add('section_ask_infos', SeparatorType::class, [
                'label' => 'Information générales',
            ])
            ->add('enterpise_autocomplete', AutocompleteType::class, [
                'label' => 'Entreprise',
                'data_source_url' => '/api/autocomplete?query=:query&source=sirene',
                'data_keys' => [
                    'id'          => 'siret',
                    'label'       => 'uniteLegale.denominationUniteLegale',
                    'siren'       => 'siren',
                    'emailReferentEntreprise' => 'emailReferentEntreprise',
                    'telephoneReferentEntreprise' => 'telephoneReferentEntreprise',
                    'naf'         => 'uniteLegale.activitePrincipaleUniteLegale',
                    'address'     => 'adresseEtablissement.numeroVoieEtablissement_adresseEtablissement.typeVoieEtablissement_adresseEtablissement.libelleVoieEtablissement',
                    'postal_code' => 'adresseEtablissement.codePostalEtablissement',
                    'city'        => 'adresseEtablissement.libelleCommuneEtablissement',
                    'country'     => '[[France]]',
                    'vat_number'  => 'calculateTVA(siren)'
                ],
                'data_targets' => [
                    'id'          => '#person_access_step_one_form_siret',
                    'label'       => '#person_access_step_one_form_company_name',
                    'siren'       => '#person_access_step_one_form_siren',
                    'naf'         => '#person_access_step_one_form_naf',
                    'address'     => '#person_access_step_one_form_address',
                    'postal_code' => '#person_access_step_one_form_postal_code',
                    'city'        => '#person_access_step_one_form_city',
                    'country'     => '#person_access_step_one_form_country',
                    'vat_number'  => '#person_access_step_one_form_vat_number',
                    'emailReferentEntreprise' => '#person_access_step_one_form_security_officer_email',
                    'telephoneReferentEntreprise' => '#person_access_step_one_form_security_officer_phone',
                ],
            ])
            ->add('request_date', DateType::class, [
                'label' => 'Date de la demande',
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => $this->lockIfFilled((new \DateTime())->format('Y-m-d')),
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value'   => (new \DateTime())->format('Y-m-d'),
                        'message' => 'La date doit être postérieure à aujourd’hui.',
                    ]),
                ],
            ])
            ->add('company_name', TextType::class, [
                'label' => 'Raison sociale',
                'data' => $defaultData['company_name'],
                'attr' => $this->lockIfFilled($defaultData['company_name']),
            ])
            ->add('section_address', SeparatorType::class, [
                'label' => 'Coordonnées de la société',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse de la société',
                'data' => $defaultData['address'],
                'attr' => $this->lockIfFilled($defaultData['address']),
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code postal',
                'data' => $defaultData['postal_code'],
                'attr' => $this->lockIfFilled($defaultData['postal_code']),
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'data' => $defaultData['city'],
                'attr' => $this->lockIfFilled($defaultData['city']),
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'data' => $defaultData['country'],
                'attr' => $this->lockIfFilled($defaultData['country']),
            ])
            ->add('section_enterprise', SeparatorType::class, [
                'label' => 'Entreprise',
            ])
            ->add('siren', TextType::class, [
                'label' => 'Numéro SIREN',
                'data' => $defaultData['siren'],
                'attr' => $this->lockIfFilled($defaultData['siren']),
            ])
            ->add('naf', TextType::class, [
                'label' => 'Code NAF',
                'data' => $defaultData['naf'],
                'attr' => $this->lockIfFilled($defaultData['naf']),
            ])
            ->add('siret', TextType::class, [
                'label' => 'Numéro SIRET',
                'data' => $defaultData['siret'],
                'attr' => $this->lockIfFilled($defaultData['siret']),
            ])
            ->add('vat_number', TextType::class, [
                'label' => 'Numéro de TVA intracommunautaire',
                'data' => $defaultData['vat_number'],
                'attr' => $this->lockIfFilled($defaultData['vat_number']),
            ])
            ->add('section_mandatory_access', SeparatorType::class, [
                'label' => 'Accès requis',
            ])
            ->add('access_duration', ChoiceType::class, [
                'label' => 'Sélectionnez la durée de l\'intervention',
                'choices' => [
                    'Permanent' => 'permanent',
                    'Temporaire' => 'temporaire',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('access_type', ChoiceType::class, [
                'label' => 'Type d\'accès',
                'choices' => [
                    'Première édition' => 'first',
                    'Renouvellement habilitation' => 'renewal',
                    'Duplicata' => 'duplicate',
                    'Changement d\'entreprise' => 'duplicate',
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
            ->add('access_locations', ChoiceType::class, [
                'label' => 'Lieux d\'accès',
                'choices' => [
                    'IP Fos-sur-Mer' => 'fos',
                    'IP Lavéra' => 'lavera',
                    'Siège social' => 'hq',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('access_purpose', TextareaType::class, [
                'label' => 'Motif d\'accès',
            ])
            ->add('section_refsec', SeparatorType::class, [
                'label' => 'Référent Sûreté',
            ])
            ->add('security_officer_name', TextType::class, [
                'label' => 'Nom et prenom du référent sûreté',
                'attr' => $this->lockIfFilled($defaultData['security_officer_name'] ?? null),
            ])
            ->add('security_officer_position', TextType::class, [
                'label' => 'Fonction du référent sûreté',
                'data' => $defaultData['security_officer_position'],
                'attr' => $this->lockIfFilled($defaultData['security_officer_position']),
            ])
            ->add('security_officer_email', TextType::class, [
                'label' => 'Email du référent sûreté',
                'data' => $defaultData['security_officer_email'],
                'attr' => $this->lockIfFilled($defaultData['security_officer_email']),
            ])
            ->add('security_officer_phone', TextType::class, [
                'label' => 'Numéro de téléphone portable du référent sûreté',
                'data' => $defaultData['security_officer_phone'],
                'attr' => $this->lockIfFilled($defaultData['security_officer_phone']),
            ])
            ->add('section_alternate_referent', SeparatorType::class, [
                'label' => 'Référent Suppléant',
            ])
            ->add('alternate_referent_name', TextType::class, [
                'label' => 'Nom et prenom du référent suppléant',
                'data' => $this->nameGuesser->guessName($defaultData['alternate_referent_email']),
                'attr' => $this->lockIfFilled($defaultData['alternate_referent_name'] ?? null),
            ])
            ->add('alternate_referent_position', TextType::class, [
                'label' => 'Fonction du référent suppléant',
                'data' => $defaultData['alternate_referent_position'],
                'attr' => $this->lockIfFilled($defaultData['alternate_referent_position']),
            ])
            ->add('alternate_referent_email', TextType::class, [
                'label' => 'Email du référent suppléant',
                'data' => $defaultData['alternate_referent_email'],
                'attr' => $this->lockIfFilled($defaultData['alternate_referent_email']),
            ])
            ->add('alternate_referent_phone', TextType::class, [
                'label' => 'Numéro de téléphone portable du référent suppléant',
                'data' => $defaultData['alternate_referent_phone'],
                'attr' => $this->lockIfFilled($defaultData['alternate_referent_phone']),
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
            'required' => false
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getRules(): object
    {
        return $this->rules;
    }

    private function lockIfFilled(?string $value): array
    {
        return ['class' => !empty(trim($value)) ? 'read_only' : ''];
    }
}
