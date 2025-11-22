<?php

namespace App\MultiStepBundle\Domain\Person\Rules;

use App\MultiStepBundle\Application\PersonAccessWorkflowService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PersonAccessRulesStepSix implements PersonAccessRulesInterface
{
    public function __construct(private readonly PersonAccessWorkflowService $workflowService)
    {
    }

    public function getUselessFields(array $currentData): array
    {
        $previousData = $this->workflowService->getAllData();
        $stepOne = $previousData['person_step_one'] ?? [];
        $accessDuration = strtolower($stepOne['access_duration'] ?? '');

        if ($accessDuration === 'temporaire') {
            return [
                'signature',
                'card_place',
                // Champs conditionnels selon le rôle
                'document_validation',
                'access_decision',
                'access_duration',
            ];
        }
        return [];
    }

    public function getFieldOverrides(array $currentData): array
    {
        // Tu obtiens ici toutes les données précédentes
        $allData = $this->workflowService->getAllData();
        $locations = $allData['person_step_one']['access_locations'] ?? [];

        $labelMap = [
            'fos' => 'IP Fos-sur-Mer',
            'lavera' => 'IP Lavéra',
        ];

        $choices = [];
        foreach ($locations as $loc) {
            if (isset($labelMap[$loc])) {
                $choices[$labelMap[$loc]] = $loc;
            }
        }

        // S'il n'y a pas de choix valides, ne rien changer
        if (empty($choices)) {
            return [];
        }

        // Retourne une structure standard avec les modifications de champ à appliquer
        return [
            'card_place' => [
                'type' => ChoiceType::class,
                'options' => [
                    'label' => 'Lieu de retrait du titre de circulation(TC) fluxel',
                    'choices' => $choices,
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                ],
            ],
        ];
    }
}