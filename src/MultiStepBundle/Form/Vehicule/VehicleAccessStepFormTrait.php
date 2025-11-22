<?php

namespace App\MultiStepBundle\Form\Vehicule;

use App\Form\SeparatorType;
use App\MultiStepBundle\Form\Person\DataTransformer\StringToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

trait VehicleAccessStepFormTrait
{

    public function updateBuilderAddViewTransformer(FormBuilderInterface $builder): void {
        // Transformer partagé
        $dateTransformer = new StringToDateTimeTransformer();

        // Parcours automatique de tous les champs du builder
        foreach ($builder->all() as $name => $child) {
            $typeClass = $child->getType()->getInnerType();
            if ($typeClass instanceof DateType || $typeClass::class === DateType::class) {
                $builder->get($name)->addViewTransformer($dateTransformer, true);
            }
        }
    }

    public function addDynamicFieldListener(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form     = $event->getForm();
            $data     = (array) $event->getData();
            $useless  = $this->getRules()->getUselessFields($data);

            foreach ($useless as $field) {
                if ($form->has($field)) {
                    $form->remove($field);
                }
            }

            if (method_exists($this->rules, 'getFieldOverrides')) {
                $overrides = $this->rules->getFieldOverrides($data);

                foreach ($overrides as $field => $definition) {
                    if ($form->has($field)) {
                        $form->add($field, $definition['type'], $definition['options']);
                    }
                }
            }

            // Remove empty sections
            // Collect current field names in order
            $fields = array_keys($form->all());
            foreach ($fields as $i => $name) {
                $child = $form->get($name);
                if ($child->getConfig()->getType()->getInnerType() instanceof SeparatorType) {
                    // gather subsequent fields until next separator
                    $hasField = false;
                    for ($j = $i + 1; $j < count($fields); $j++) {
                        $next = $fields[$j];
                        $nextChild = $form->get($next);
                        if ($nextChild->getConfig()->getType()->getInnerType() instanceof SeparatorType) {
                            break;
                        }
                        // if this field exists, section not empty
                        $hasField = true;
                        break;
                    }
                    if (!$hasField) {
                        $form->remove($name);
                    }
                }
            }
        });
    }

    /**
     * Must return the corresponding rules service instance
     */
    abstract protected function getRules(): object;
}