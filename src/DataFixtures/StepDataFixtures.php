<?php
// src/DataFixtures/StepDataFixtures.php
namespace App\DataFixtures;

use App\MultiStepBundle\Entity\StepData;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class StepDataFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $em): void
    {
        // ===== 1) Résolution des utilisateurs (email connu -> rôle -> premier user) =====
        $pickByEmailThenRole = function(?string $email = null, ?string $role = null) use ($em): User {
            $repo = $em->getRepository(User::class);

            if ($email) {
                if ($u = $repo->findOneBy(['email' => $email])) return $u;
            }
            if ($role) {
                // recherche LIKE dans la colonne JSON textuelle "roles"
                $qb = $repo->createQueryBuilder('u')
                    ->where('u.roles LIKE :r')
                    ->setParameter('r', '%"'.$role.'"%')
                    ->setMaxResults(1);
                if ($u = $qb->getQuery()->getOneOrNullResult()) return $u;
            }
            $u = $repo->findOneBy([]); // fallback
            if (!$u) {
                throw new \RuntimeException('Aucun utilisateur disponible pour les StepData fixtures.');
            }
            return $u;
        };

        // Adapte si tu veux coller à ta liste (vue phpMyAdmin) :
        $uUser   = $pickByEmailThenRole('amine.bensaid@gmail.com', 'ROLE_USER');
        $uRefsec = $pickByEmailThenRole(null, 'ROLE_REFSECU');   // ton guard utilise ROLE_REFSECU
        $uSdri   = $pickByEmailThenRole(null, 'ROLE_SDRI');
        $uAdmin  = $pickByEmailThenRole(null, 'ROLE_ADMIN');
        $uGuard  = $pickByEmailThenRole(null, 'ROLE_GARDIEN');

        // ===== 2) Fabrique de payloads réalistes & variés =====
        $payload = function (int $i, array $over = []): array {
            $today = new \DateTimeImmutable('today');
            $person = [
                'person_step_one' => [
                    'request_date' => $today->format('Y-m-d'),
                    'company_name' => ['Entreprise A SARL','Beta Industrie SAS','Gamma Énergies','Delta IT','Oméga Services'][$i%5],
                    'address'      => ($i+1).' Rue '.['Alpha','Bêta','Gamma','Delta','Epsilon'][$i%5],
                    'postal_code'  => str_pad((string)(75000 + $i%20), 5, '0', STR_PAD_LEFT),
                    'city'         => ['Paris','Lyon','Marseille','Bordeaux','Lille'][$i%5],
                    'country'      => 'France',
                    'siren'        => str_pad((string)(111111111 + $i), 9, '0', STR_PAD_LEFT),
                    'naf'          => '6201Z',
                    'siret'        => str_pad((string)(11111111100011 + $i), 14, '0', STR_PAD_LEFT),
                    'vat_number'   => 'FR'.(111111111 + $i),
                    'access_duration'  => $i%2 ? 'temporaire' : 'permanent',
                    'access_type'      => $i%7===0 ? 'duplicate' : ($i%3===0 ? 'renewal' : 'first'),
                    'duplicate_reason' => $i%7===0 ? ['loss','breaks','theft'][$i%3] : null,
                    'access_locations' => array_slice(['fos','lavera','hq'], 0, 1 + ($i%3)),
                    'access_purpose'   => 'Intervention technique (BT/ATEX) zone IP.',
                    'security_officer_name'     => ['Alice Alpha','Bruno Beta','Carla Gamma'][$i%3],
                    'security_officer_position' => 'Référent sûreté',
                    'security_officer_email'    => ['alice.alpha@example.com','bruno.beta@example.com','carla.gamma@example.com'][$i%3],
                    'security_officer_phone'    => '06'.str_pad((string)($i+10000000), 8, '0', STR_PAD_LEFT),
                    'alternate_referent_name'   => null,
                    'alternate_referent_position'=> null,
                    'alternate_referent_email'  => null,
                    'alternate_referent_phone'  => null,
                ],
                'person_step_two' => [
                    'gender' => $i%2 ? 'm' : 'mme',
                    'cni_type' => ['passeport','cni','sejour'][$i%3],
                    'matricule' => 'EMP'.str_pad((string)(12000+$i),5,'0',STR_PAD_LEFT),
                    'numero_cni' => 'X'.(100000000+$i),
                    'employee_first_name' => ['Jean','Marie','Rachid','Nadia','Lucas'][$i%5],
                    'employee_last_name'  => ['Dupont','Martin','Nguyen','Diallo','Schmidt'][$i%5],
                    'employee_last_name_2'=> ['Pierre','Anne','Ali','Fatou','Paul'][$i%5],
                    'employee_last_name_3'=> null,
                    'employee_last_name_4'=> null,
                    'maiden_name' => null,
                    'employee_birthdate' => '198'.($i%10).'-0'.(1+($i%9)).'-1'.($i%9),
                    'employee_birth_postale_code' => '75001',
                    'employee_birthplace' => 'Paris',
                    'employee_birth_district' => '1er arrondissement',
                    'nationality' => ['Français','Cameroun','Maroc','Sénégal','Algérie'][$i%5],
                    'social_security_number' => '1900'.(5991234567+$i),
                    'employee_email' => 'user'.$i.'@example.com',
                    'employee_phone' => '06'.str_pad((string)(22000000+$i), 8, '0', STR_PAD_LEFT),
                    'employee_refugee' => [],
                    'section_employee_address' => (10+$i).' Rue du Port',
                    'postal_code' => '7500'.($i%9),
                    'city' => ['Paris','Lyon','Marseille','Bordeaux','Lille'][$i%5],
                    'country' => 'France',
                    'resident_situation' => $i%4===0 ? 'hosted' : 'owner_or_tenant',
                    'father_name' => 'Parent',
                    'father_first_name' => 'Pierre',
                    'mother_maiden_name' => 'Martin',
                    'mother_first_name' => 'Marie',
                    'contract_type' => $i%3 ? 'cdi' : 'cdd',
                    'employee_function' => ['Technicien Sécurité','Électricien','Mécanicien','Ingénieur','Opérateur'][$i%5],
                    'employment_date' => '2019-01-0'.(1+($i%8)),
                    'contract_end_date' => $i%3 ? null : '2026-12-31',
                ],
                'person_step_three' => [
                    'fluxel_training' => $today->modify('+'.(2+$i%5).' day')->format('Y-m-d'),
                    'gies'            => $today->modify('+'.(8+$i%7).' day')->format('Y-m-d'),
                    'atex'            => $today->modify('+'.(6+$i%6).' day')->format('Y-m-d'),
                    'zar'             => $i%6===0 ? $today->modify('+30 day')->format('Y-m-d') : null,
                    'health'          => $today->modify('+'.(5+$i%5).' day')->format('Y-m-d'),
                ],
                'person_step_five' => [
                    'passport'              => '/uploads/persons/person_step_five/passport'.$i.'.jpg',
                    'photo'                 => '/uploads/persons/person_step_five/photo'.$i.'.jpg',
                    'proof_of_address_host' => '/uploads/persons/person_step_five/proof'.$i.'.pdf',
                    'doc_atex_0'            => '/uploads/persons/person_step_five/atex'.$i.'.pdf',
                    'doc_gies_1'            => '/uploads/persons/person_step_five/gies'.$i.'.pdf',
                    'health_attestation'    => '/uploads/persons/person_step_five/health'.$i.'.pdf',
                    'birth_certificate'      => null,
                    'criminal_record_origin' => null,
                    'criminal_record_nationality' => null,
                    'criminal_record_resident_country' => null,
                ],
                'person_step_six' => [],
            ];

            return array_replace_recursive($person, $over);
        };

        // ===== 3) Créateur d’entité StepData =====
        $create = function(User $user, string $status, int $i, array $over = []) use ($em, $payload): StepData {
            $sd = new StepData();
            $sd->setUser($user);
            $sd->setStepNumber(sprintf('%s-%s-%s',
                strtoupper($payload($i)['person_step_two']['employee_last_name']),
                $payload($i)['person_step_one']['siret'],
                (new \DateTimeImmutable())->format('YmdHis').sprintf('%02d',$i)
            ));
            $sd->setStepType('person');
            $sd->setPersistanceType('single_table');
            $sd->setData($payload($i, $over));
            $sd->setStatus($status);
            $em->persist($sd);
            return $sd;
        };

        // ===== 4) Crée des StepData sur TOUTES les places du workflow =====
        $rows = [];
        $rows[] = $create($uUser,   'draft',               1);
        $rows[] = $create($uUser,   'deposit',             2);
        $rows[] = $create($uUser,   'awaiting_reference',  3);
        $rows[] = $create($uRefsec, 'pending',             4);
        $rows[] = $create($uSdri,   'awaiting_info',       5, ['person_step_one'=>['access_purpose'=>'Préciser zone exacte et durée.']]);
        $rows[] = $create($uSdri,   'provisioned',         6);
        $rows[] = $create($uAdmin,  'approved',            7);
        $rows[] = $create($uSdri,   'refused',             8, ['person_step_one'=>['access_purpose'=>'Dossier incomplet – refus.']]);

        // Phase technique + KO
        $rows[] = $create($uSdri,   'microcesame',         9);
        $rows[] = $create($uSdri,   'microcesame_ko',     10, ['person_step_one'=>['access_purpose'=>'Erreur Microcésame – SIRET non reconnu.']]);
        $rows[] = $create($uSdri,   'enquete_prealable',  11);
        $rows[] = $create($uSdri,   'investigation_ko',   12);
        $rows[] = $create($uSdri,   'tc_temp_ok',         13);
        $rows[] = $create($uSdri,   'cerbere_sent',       14);
        $rows[] = $create($uSdri,   'cerbere_ko',         15);
        $rows[] = $create($uSdri,   'cerbere_ok',         16);

        // Paiement → édition → livraison
        $rows[] = $create($uUser,   'awaiting_payment',   17);
        $rows[] = $create($uRefsec, 'paid',               18);
        $rows[] = $create($uSdri,   'card_edited',        19);
        $rows[] = $create($uGuard,  'card_delivered',     20);

        // Cas particuliers
        $rows[] = $create($uSdri,   'bad_firm',           21, ['person_step_one'=>['company_name'=>'Société XYZ (non éligible)']]);
        $rows[] = $create($uSdri,   'payment_doc_ko',     22);

        // Références éventuelles
        foreach ($rows as $k => $sd) {
            // $this->addReference('stepdata_'.$k, $sd);
        }

        $em->flush();
    }

    public static function getGroups(): array
    {
        return ['system-data', 'dev', 'demo'];
    }
}
