<?php

namespace App\AdminBundle\Infrastructure\Symfony\Form;

use App\AdminBundle\Application\Port\EntityServiceInterface;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntityFormType extends AbstractType
{
    public function __construct(private readonly HtmlSanitizerInterface $sanitizer) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var EntityServiceInterface $entityService */
        $entityService = $options['entity_service'];
        $entityClass = $entityService->getEntityClass();

        $properties = (new ReflectionExtractor())->getProperties($entityClass);

        foreach ($properties as $property) {
            $type = $this->guessType($entityClass, $property);
            if (!$type) {
                continue; // ignore unguessable types
            }

            $builder->add($property, $type, [
                'required' => false,
                'label' => ucfirst($property),
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
        $resolver->setRequired('entity_service');
        $resolver->setAllowedTypes('entity_service', EntityServiceInterface::class);
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    private function guessType(string $class, string $property): ?string
    {
        $extractor = new ReflectionExtractor();
        $type = $extractor->getTypes($class, $property)[0] ?? null;

        if (!$type) return TextType::class;

        return match ($type->getBuiltinType()) {
            'int'     => IntegerType::class,
            'float'   => NumberType::class,
            'bool'    => CheckboxType::class,
            'array'   => CollectionType::class,
            'object'  => $type->getClassName() === DateTimeInterface::class
                ? DateTimeType::class
                : TextType::class,
            default   => TextType::class,
        };
    }

    public function getBlockPrefix(): string
    {
        return 'dynamic_entity';
    }
}
