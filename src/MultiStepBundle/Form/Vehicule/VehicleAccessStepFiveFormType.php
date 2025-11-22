<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\MultiStepBundle\Domain\Vehicule\Rules\VehicleAccessRulesStepFive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleAccessStepFiveFormType extends AbstractType
{
    use VehicleAccessStepFormTrait;

    public function __construct(
        private readonly VehicleAccessRulesStepFive $rules,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('signature', FileType::class, [
                'label' => 'Signature du responsable et Cachet (image)',
                'required' => false,
            ])
            ->add('card_copy', FileType::class, [
                'label' => 'Photocopie recto/verso de la carte grise',
                'required' => false,
            ])
            ->add('gies_sticker_copy', FileType::class, [
                'label' => 'Photocopie du macaron GIES',
                'required' => false,
            ])
            ->add('old_circulation_card', FileType::class, [
                'label' => 'Ancien titre de circulation',
                'required' => false,
            ])
            ->add('declaration_form', FileType::class, [
                'label' => 'Formulaire de déclaration de perte, casse ou vol',
                'required' => false,
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
