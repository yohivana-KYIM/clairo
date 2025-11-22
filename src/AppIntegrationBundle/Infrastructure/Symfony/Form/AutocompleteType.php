<?php

namespace App\AppIntegrationBundle\Infrastructure\Symfony\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        // require a data-source-url option
        $resolver->setRequired(['data_source_url']);
        // allow mapping keys and targets
        $resolver->setDefined(['data_keys', 'data_targets']);
        $resolver->setDefaults([
            'data_keys'   => [], // e.g. ['id' => 'id', 'label' => 'name', 'city' => 'address.city']
            'data_targets'=> [], // e.g. ['id' => '#company_id', 'city' => '#company_city']
            'mapped'       => false, // make field unmapped by default
            'attr' => [
                'class' => 'api-js-autocomplete',
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // pass variables to the Twig template
        $view->vars['data_source_url'] = $options['data_source_url'];
        $view->vars['data_keys']       = $options['data_keys'];
        $view->vars['data_targets']    = $options['data_targets'];
    }
    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'autocomplete';
    }
}