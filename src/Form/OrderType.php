<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            // ->add('adresse', EntityType::class,[
            //     'label' => false,
            //     'required' => true,
            //     'class' => Entreprise::class,
            //     'choice_label' => 'adresse',
            //     'multiple' => false,
            //     'expanded' => true
            // ])
            // ->add('nom')

            // ->add('Carrier', EntityType::class,[
            //     'label' => 'Choisissez votre transporter',
            //     'required' => true,
            //     'class' => Carrier::class,
            //     'multiple' => false,
            //     'expanded' => true
            // ])

            ->add('submit', SubmitType::class,[
                'label' => 'Valider ma commande',
                'attr' => [
                    'class' => 'btn btn-success btn-lg'
                ]
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
            'user' => array()
        ]);
    }
}
