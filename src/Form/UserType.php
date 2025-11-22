<?php

namespace App\Form;

use DateTime;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly HtmlSanitizerInterface $sanitizer
    ) {

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'label_attr' => ['class' => 'h5'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'label_attr' => ['class' => 'h5'],
                'attr' => [
                    'class' => 'is-invalid',
                ],
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'SDRI' => 'ROLE_SDRI',
                    'RefSecuEnt' => 'ROLE_REFSECU',
                    'Gardien' => 'ROLE_GARDIEN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('mfaStrategies', ChoiceType::class, [
                'label' => 'Méthode de connexion',
                'label_attr' => ['class' => 'h5'],
                'attr' => [
                    'class' => 'is-invalid',
                ],
                'choices' => [
                    'Google Authenticator' => 'google',
                    'Microsoft Authenticator' => 'totp',
                    'Email' => 'email',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('isVerified', ChoiceType::class, [
                'label' => 'Compte vérifié',
                'label_attr' => ['class' => 'h5'],
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
            ]);

        if ($this->security?->getUser()?->hasRole('ROLE_REFSECU') || $this->security?->getUser()?->hasRole('ROLE_ADMIN')) {
            $builder->add('isReferentVerified', ChoiceType::class, [
                'label' => 'Vérifié par le referent sécurité',
                'label_attr' => ['class' => 'h5'],
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
            ]);
        }

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
            'data_class' => User::class,
        ]);
    }
}
