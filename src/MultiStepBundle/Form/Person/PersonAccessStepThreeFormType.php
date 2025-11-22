<?php

namespace App\MultiStepBundle\Form\Person;

use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Person\Rules\PersonAccessRulesStepThree;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class PersonAccessStepThreeFormType extends AbstractType
{
    use PersonAccessStepFormTrait;

    public function __construct(
        private readonly PersonAccessRulesStepThree $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Date de référence immuable
        $today = new \DateTimeImmutable('today');

        $builder
            ->add('section_training', SeparatorType::class, [
                'label' => 'Formations',
            ])

            // Accueil sécurité Fluxel — saisie de la date de fin de validité
            ->add('fluxel_training', DateType::class, $this->buildExpiryDateFieldOptions(
                label: 'Accueil sécurité Fluxel — date de fin de validité',
                today: $today
            ))

            ->add('section_certs', SeparatorType::class, [
                'label' => 'Certifications',
            ])

            // GIES (champ unique, remplace GIES 1 & 2)
            ->add('gies', DateType::class, $this->buildExpiryDateFieldOptions(
                label: 'GIES — date de fin de validité',
                today: $today
            ))

            // ATEX (remplace ATEX 0)
            ->add('atex', DateType::class, $this->buildExpiryDateFieldOptions(
                label: 'ATEX — date de fin de validité',
                today: $today
            ))

            // ZAR
            ->add('zar', DateType::class, $this->buildExpiryDateFieldOptions(
                label: 'Habilitation ZAR — date de fin de validité',
                today: $today
            ))

            // Visite médicale (renommé côté label)
            ->add('health', DateType::class, $this->buildExpiryDateFieldOptions(
                label: 'Visite médicale — date de fin de validité',
                today: $today
            ));

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

    /**
     * Options standardisées pour un champ DateType où l’on saisit la **date de fin de validité**.
     * - Autorise dates expirées (passé) et dates futures (prochaine échéance).
     * - Fenêtre +/- 10 ans autour d’aujourd’hui pour éviter les saisies aberrantes.
     */
    private function buildExpiryDateFieldOptions(string $label, \DateTimeImmutable $today): array
    {
        $minDate = $today->sub(new \DateInterval('P10Y'));
        $maxDate = $today->add(new \DateInterval('P10Y'));

        return [
            'label'   => $label,
            'widget'  => 'single_text',
            'attr'    => [
                'min'         => $minDate->format('Y-m-d'),
                'max'         => $maxDate->format('Y-m-d'),
                'placeholder' => 'YYYY-MM-DD',
            ],
            'constraints' => [
                new GreaterThanOrEqual([
                    'value'   => $minDate->format('Y-m-d'),
                    'message' => sprintf('La date doit être postérieure au %s.', $minDate->format('d/m/Y')),
                ]),
                new LessThanOrEqual([
                    'value'   => $maxDate->format('Y-m-d'),
                    'message' => sprintf('La date doit être antérieure au %s.', $maxDate->format('d/m/Y')),
                ]),
            ],
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'required'   => false,
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
