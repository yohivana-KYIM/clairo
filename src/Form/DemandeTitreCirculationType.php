<?php

namespace App\Form;

use App\Entity\DemandeTitreCirculation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DemandeTitreCirculationType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('created_at')
            ->add('validated_at')
            ->add('ip')
            ->add('intervention')
            ->add('etatcivil')
            ->add('filiation')
            ->add('adresse')
            ->add('infocomplementaire')
            // ->add('entreprise')
            ->add('docpersonnel')
            ->add('documentprofessionnel')
            ->add('user')
            ->add('status', ChoiceType::class, [
                'placeholder' => 'Choisissez le status de votre demande',
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    DemandeTitreCirculation::STATUS_DEPOSIT => DemandeTitreCirculation::STATUS_DEPOSIT,
                    DemandeTitreCirculation::STATUS_PENDING => DemandeTitreCirculation::STATUS_PENDING,
                    DemandeTitreCirculation::STATUS_AWAITING => DemandeTitreCirculation::STATUS_AWAITING,
                    DemandeTitreCirculation::STATUS_PROVISIONED => DemandeTitreCirculation::STATUS_PROVISIONED,
                    DemandeTitreCirculation::STATUS_GRANTED => DemandeTitreCirculation::STATUS_GRANTED,
                    DemandeTitreCirculation::STATUS_DENIED => DemandeTitreCirculation::STATUS_DENIED,
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
            'data_class' => DemandeTitreCirculation::class,
        ]);
    }
}
