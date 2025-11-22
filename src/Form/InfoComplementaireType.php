<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\DemandeTitreCirculation;
use PharIo\Manifest\Email;
use App\Form\EntrepriseType;
use App\Entity\InfoComplementaire;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InfoComplementaireType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numTelephone', TextType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?(?:\d{1,4}[-\s])?\d{1,15}$/',
                        'message' => 'Le numéro de téléphone est invalide',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Numero Téléphone (ex : +33123456789)',
                    'class' => 'form-control is-invalid',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Adresse e-mail (ex : votre@email.com)',
                    'class' => 'form-control is-invalid',
                ]
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
            'data_class' => InfoComplementaire::class,
        ]);
    }
}
