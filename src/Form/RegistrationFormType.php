<?php

namespace App\Form;

use App\Entity\User;
use App\Service\Validator\Constraint\PasswordConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'mt-3 mb-3 p-2'],
                'invalid_message' => 'Un compte existe déjà avec cette adresse e-mail.',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les <a href="/pdfs/Conditions%20Générales%20d\'utilisation%20cleo.pdf" target="_blank" rel="noopener noreferrer">conditions générales d\'utilisation</a>.',
                'label_html' => true,
                'label_attr' => [ 'class' => 'link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover mb-2', 'style' => 'font-size: .9em;'],
                'attr' => ['class' => 'border-dark'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales d\'utilisation.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => "Les mots de passe doivent etre identique !",
                'label' => false,
                'required' => true,
                'constraints' => [
                            new NotBlank([
                                'message' => 'Veuillez saisir un mot de passe',
                            ]),
                            new PasswordConstraint()
                        ],
                'first_options' => ['label'=> false,
                ],
                'second_options' => ['label' => false,
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
            'data_class' => User::class,
        ]);
    }
}
