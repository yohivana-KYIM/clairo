<?php

// src/Form/SettingsType.php

namespace App\Form;

use App\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FieldType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly HtmlSanitizerInterface $sanitizer
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Setting[] $settings */
        $settings = $options['settings'];
        foreach ($settings as $setting) {
            $fieldOptions = [
                'required' => false,
                'label' => $this->translator->trans($setting->getLabel()),
                'data' => $setting->getValue(),
            ];

            $fieldType = match ($setting->getType()) {
                'int' => FieldType\IntegerType::class,
                'bool' => FieldType\CheckboxType::class,
                'email' => FieldType\EmailType::class,
                'select', 'radio' => FieldType\ChoiceType::class,
                default => FieldType\TextType::class,
            };

            if (in_array($setting->getType(), ['select', 'radio']) && $setting->getOptions()) {
                $choices = [];
                foreach ($setting->getOptions() as $option) {
                    $label = $this->translator->trans("settings.options.{$setting->getName()}_{$option}");
                    $choices[$label] = $option;
                }

                $fieldOptions['choices'] = $choices;
                $fieldOptions['expanded'] = $setting->getType() === 'radio';
                $fieldOptions['multiple'] = false;
            }

            $builder->add($setting->getName(), $fieldType, $fieldOptions);

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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('settings');
    }
}
