<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\MultiStepBundle\Domain\Vehicule\Rules\VehicleAccessRulesStepOne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleAccessStepSixFormType extends AbstractType
{

    public function __construct(
        private readonly HtmlSanitizerInterface $sanitizer
    )
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('terms_and_conditions', TextareaType::class, [
                'label' => 'Conditions générales',
                'data' => "Je m’engage à faire respecter et à informer les conducteurs des règles en matière de circulation routière, de sûreté et de sécurité en application dans les installations portuaires pétrolières de la société FLUXEL S.A.S. et que toute infraction entraîne le retrait immédiat du titre de circulation sans préjudice des poursuites exercées s’il y a lieu. La délivrance d'un titre de circulation est subordonnée à la possession par le conducteur, et présentation le cas échéant de tous documents concernant les conditions administratives de circulation de véhicules. Tout évènement constaté sur une installation portuaire de FLUXEL, doit impérativement être porté à la connaissance de FLUXEL sans délai.\n\nLe titre de circulation peut être retiré en cas de délit ou d’infraction aux règlements relatifs à la police du port, ou si la sécurité ou la bonne exploitation du port l’exigent. Ce titre de circulation devra être renouvelé à chaque changement de propriétaire ou de locataire, et être remis à FLUXEL en cas de cession de véhicule. Ce titre de circulation est attitré, et ne peut en aucun cas être prêté à un autre véhicule conformément à la règlementation en vigueur.\n\nJe note que le dépôt de dossier de demande de titre de circulation de FLUXEL vaut acceptation des conditions tarifaires de la brochure FLUXEL (et ce, quel que soit l’issue de l’instruction du dossier).\n\nJe m’engage à prendre les dispositions nécessaires pour que le(s) conducteur(s) puisse(nt) participer à une information concernant les principes généraux et les règles particulières de sûreté en application de l’alinéa 3 de l’article R.5332-40 du code des transports.",
                'attr' => ['readonly' => true, 'class' => 'terms-and-conditions'],
            ])
            ->add('accept_terms', CheckboxType::class, [
                'label' => 'Je reconnais avoir lu et accepté les conditions générales ci-dessus',
                'required' => true,
            ]);

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
}
