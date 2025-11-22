<?php

namespace App\MultiStepBundle\Domain\Vehicule\Rules;

use App\MultiStepBundle\Application\VehicleAccessWorkflowService;

class VehicleAccessRulesStepFive implements VehicleAccessRulesInterface
{
    public function __construct(private readonly VehicleAccessWorkflowService $workflowService)
    {
    }

    /**
     * Détermine les champs à masquer/enlever selon
     * - le type de certification (si ce n'est pas "gies", on n'a pas besoin du macaron GIES)
     * - le type d'accès (si ce n'est pas "duplication", on n'a pas besoin de l'ancien titre ni du formulaire de déclaration)
     *
     * @param array $currentData  Les données déjà soumises pour cette étape
     * @return string[]           La liste des clés à considérer comme "inutiles"
     */
    public function getUselessFields(array $currentData): array
    {
        // on récupère l'ensemble des données précédentes
        $allData = $this->workflowService->getAllData();

        // certification_type vient de l'étape 2
        $certType = $allData['vehicle_step_two']['certification_type'] ?? null;
        // access_type vient de l'étape 1
        $accessType = $allData['vehicle_step_one']['access_type'] ?? null;

        $useless = [];

        // Si ce n'est pas une certification GIES, on retire le champ du macaron
        if ($certType !== 'gies') {
            $useless[] = 'gies_sticker_copy';
        }

        // Si ce n'est pas une duplication, on retire l'ancien titre et le formulaire de déclaration
        if ($accessType !== 'duplication') {
            $useless[] = 'old_circulation_card';
            $useless[] = 'declaration_form';
        }

        return $useless;
    }
}