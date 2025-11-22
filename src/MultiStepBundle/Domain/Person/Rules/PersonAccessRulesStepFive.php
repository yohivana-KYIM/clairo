<?php

namespace App\MultiStepBundle\Domain\Person\Rules;

use App\MultiStepBundle\Application\PersonAccessWorkflowService;

class PersonAccessRulesStepFive implements PersonAccessRulesInterface
{
    public function __construct(private readonly PersonAccessWorkflowService $workflowService)
    {
    }

    public function getUselessFields(array $currentData): array
    {
        $previousData = $this->workflowService->getAllData();
        $useless = [];

        $stepOne = $previousData['person_step_one'] ?? [];
        $accessDuration = strtolower($stepOne['access_duration'] ?? '');

        if ($accessDuration === 'temporaire') {
            $useless = array_merge($useless,[
                'photo',
                'proof_of_address_host',
                'resident_letter',
                'proof_of_address_hosted',
                'zar_decision',
                'doc_atex_0',
                'doc_gies_1',
                'doc_gies_2',
                'loss_declaration',
                'health_attestation',
                'taxi_card',
                'birth_certificate',
                'criminal_record_origin',
                'criminal_record_nationality',
                'criminal_record_resident_country',
                'refugee_attestation',
                'refugee_criminal_record',
            ]);
        }

        // 1) Nationalité française → masquer les documents étrangers
        $nationality = $previousData['person_step_two']['nationality'] ?? '';
        if (str_starts_with(strtolower($nationality), 'franc')) {
            $useless = array_merge($useless, [
                'residence_permit',
                'birth_certificate',
                'criminal_record_origin',
                'criminal_record_nationality',
                'criminal_record_resident_country',
                'refugee_attestation',
                'refugee_criminal_record',
                'section_specific_cases'
            ]);
        } else {
            // Nationalité étrangère → masquer la carte d'identité française
            $useless[] = 'id_card';
        }

        if (array_key_exists('cni_type', $previousData['person_step_two'])) {
            if ($previousData['person_step_two']['cni_type'] === 'passport' || $previousData['person_step_two']['cni_type'] === 'passeport') {
                $useless = array_merge($useless, [
                    'residence_permit',
                ]);
            } else {
                $useless = array_merge($useless, [
                    'passport',
                ]);
            }
        }

        // 2) Carte taxi uniquement si 'taxi' présent dans la raison sociale
        $company = $previousData['person_step_one']['company_name'] ?? '';
        if (stripos($company, 'taxi') === false) {
            $useless[] = 'taxi_card';
        }

        $nationality = $previousData['person_step_two']['nationality'] ?? '';
        $country = $previousData['person_step_two']['country'] ?? '';
        if(str_starts_with(strtolower($nationality), 'franc') || $country === $nationality) {
            $useless[] = 'criminal_record_resident_country';
        }

        // 3) Preuve de domicile selon situation de logement
        $situation = $previousData['person_step_two']['resident_situation'] ?? '';
        if ($situation === 'hosted') {
            // Hébergé → cacher factures et pièce identité hébergeur
            $useless[] = 'proof_of_address_host';
        } else {
            // Propriétaire/locataire → cacher attestation d'hébergement
            $useless = array_merge($useless, [
                'resident_letter',
                'resident_id_card',
                'proof_of_address_hosted',
            ]);
        }

        // Specific documents linked to trainings/visits
        $stepThree = $previousData['person_step_three'] ?? [];
        if (empty($stepThree['atex'])) {
            $useless[] = 'doc_atex_0';
        }
        if (empty($stepThree['gies'])) {
            $useless[] = 'doc_gies_1';
        }
        if (empty($stepThree['health'])) {
            $useless[] = 'health_attestation';
        }
        if (empty($stepThree['zar'])) {
            $useless[] = 'zar_decision';
        }

        // Loss declaration only for duplicata
        $accessType = $previousData['person_step_one']['access_type'] ?? '';
        if ($accessType !== 'duplicate') {
            $useless[] = 'loss_declaration';
        }

        // Refugee documents only if employee_refugee is set
        $refugeeFlags = $previousData['person_step_two']['employee_refugee'] ?? [];
        if (empty($refugeeFlags) || !in_array('refugee', (array)$refugeeFlags, true)) {
            $useless = array_merge($useless, ['refugee_attestation', 'refugee_criminal_record']);
        }

        // Garder toujours: passport, photo, zar_decision, doc_atex_0,
        // doc_gies_1, doc_gies_2, health_attestation, loss_declaration

        return $useless;
    }
}