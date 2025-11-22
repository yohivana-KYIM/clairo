<?php

namespace App\MultiStepBundle\Form\Person;

use App\Form\SeparatorType;
use App\MultiStepBundle\Domain\Person\Rules\PersonAccessRulesStepSix;
use App\MultiStepBundle\Form\Person\DataTransformer\StringToBooleanTransformer;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonAccessStepSixFormType extends AbstractType
{
    use PersonAccessStepFormTrait;

    private UserInterface $user;

    public function __construct(
        private readonly PersonAccessRulesStepSix $rules,
        private readonly Security $security,
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
        $this->user = $this->security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('general_conditions', TextareaType::class, [
                'label' => 'Conditions générales',
                'mapped' => false,
                'attr' => ['readonly' => true, 'class' => 'terms-and-conditions'],
                'data' => "J’ai pris connaissance et je m’engage à respecter les règles de sureté et de sécurité en application dans les installations
portuaires pétrolières de la société FLUXEL S.A.S. Toute infraction pourra entraîner le retrait immédiat du titre de circulation
sans préjudice des poursuites exercées s’il y a lieu. Je m’engage à porter à la connaissance de FLUXEL, sans délai, tout
évènement constaté sur une installation portuaire de FLUXEL, et à transmettre à mon employeur, une copie de la décision
préfectorale d'habilitation d’accès en Z.A.R. (reçue à mon domicile en LRAR).
Le titre de circulation peut être retiré en cas de délit ou d’infraction aux règlements relatifs à la police du port, ou si la
sécurité ou la bonne exploitation du port l’exigent.
Ce titre de circulation devra être renouvelé à chaque changement de profession ou d’employeur et être remis à celui-ci en
cas de rupture du contrat.
Ce titre de circulation est nominatif, et ne peut en aucun cas être prêté à une tierce personne conformément à la
règlementation en vigueur.
Je m’engage à participer à une information concernant les principes généraux et les règles particulières de sûreté en
application de l’alinéa 3 de l’article R.5332-40 du code des transports.",
            ])
            ->add('accept_terms', CheckboxType::class, [
                'label' => 'Je reconnais avoir lu et accepté les conditions générales ci-dessus',
                'required' => true,
            ]);
            $builder->get('accept_terms')->addModelTransformer(new StringToBooleanTransformer());
/**
        if (in_array('ROLE_ADMIN', $this->user->getRoles()) || in_array('ROLE_SDRI', $this->user->getRoles())) {
            $builder
                ->add('section_admin', SeparatorType::class, [
                    'label' => 'Actions administratives',
                ])
                ->add('observations', TextareaType::class, [
                    'label' => 'Observations',
                    'mapped' => false,
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                    ],
                ])
                ->add('document_validation', CheckboxType::class, [
                    'label' => 'Tous les documents requis sont validés',
                    'mapped' => false,
                    'required' => true,
                ])
                ->add('access_decision', ChoiceType::class, [
                    'label' => 'Décision d\'accès',
                    'choices' => [
                        'Favorable' => 'favorable',
                        'Défavorable' => 'defavorable',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                ])
                ->add('access_expiration_date', DateType::class, [
                    'label' => 'Durée de validité de l\'accès',
                    'widget' => 'single_text',
                    'required' => true,
                ]);

            $builder
                ->add('signature_admin', TextType::class, [
                    'label' => 'Nom et signature de l\'administrateur',
                    'mapped' => false,
                    'required' => true,
                ])
                ->add('signature', FileType::class, [
                    'label' => 'Télécharger la signature (image)',
                    'required' => false,
                ])
                ->add('card_place', ChoiceType::class, [
                    'label' => 'Lieu de retrait du titre de circulation(TC) fluxel',
                    'choices' => [
                        'IP Fos-sur-Mer' => 'fos',
                        'IP Lavéra' => 'lavera',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                ]);
        }
 */
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
