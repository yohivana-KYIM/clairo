<?php

namespace App\Controller\sdri;

use App\Entity\User;
use App\Entity\MailAppli;
use App\Entity\Entreprise;
use App\Entity\ProblemeCarte;
use App\Entity\DemandeTitreCirculation;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/sdri')]
class MesDemandesSDRIController extends AbstractController
{
    #[Route('/demandesdri', name: 'app_demande_sdri')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $EntrepriseCount = $EntrepriseRepository->count([]);

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findAll();
        $userCount = $userRepository->count([]);
        $demandeRepository = $entityManager->getRepository(DemandeTitreCirculation::class);
        $demandeRepositoryAll = $entityManager->getRepository(DemandeTitreCirculation::class)->findAll();

        $demandeRepoInvalid = $entityManager->getRepository(DemandeTitreCirculation::class)->findby(['validated_at' => null]);

        $statusList = [
            DemandeTitreCirculation::STATUS_DEPOSIT,
            DemandeTitreCirculation::STATUS_PENDING,
            DemandeTitreCirculation::STATUS_AWAITING_PAYMENT,
            DemandeTitreCirculation::STATUS_AWAITING,
            DemandeTitreCirculation::STATUS_PROVISIONED,
            DemandeTitreCirculation::STATUS_DENIED,
            DemandeTitreCirculation::STATUS_GRANTED,
            DemandeTitreCirculation::STATUS_PAID,
            DemandeTitreCirculation::STATUS_CARD_EDITED,
            DemandeTitreCirculation::STATUS_CARD_DELIVERED,
        ];

        // Fetch all demandes
        $demandes = $demandeRepository->findByStatuses($statusList);
        $demandesCount = $demandeRepository->countByStatuses($statusList);
        $countValid = $demandeRepository->countValidatedByStatuses($statusList);
        $countInvalid = $demandeRepository->countInvalidByStatuses($statusList);

        $problemeCarteRepository = $entityManager->getRepository(ProblemeCarte::class);
        $problemeCarte = $problemeCarteRepository->findAll();

        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $Entreprise = $EntrepriseRepository->findAll();


        return $this->render('sdri/index.html.twig', [
            'controller_name' => 'MesDemandesSDRIController',
            'demandes' => $demandes,
            'problemeCarte' => $problemeCarte,
            'entreprise' => $Entreprise,
            'entrepriseCount' => $EntrepriseCount,
            'user' => $user,
            'userCount' => $userCount,
            'demandesCount' => $demandesCount,
            'countValidated' => $countValid,
            'countInvalidated' => $countInvalid,
            'demandeRepository' => $demandeRepositoryAll,
        ]);
    }

    #[Route('/{id}/titredemandesdri/{parametre}', name: 'app_demande_sdri_status_titre')]
    public function changementStatusTitre(Request $request, EntityManagerInterface $entityManager, NotificationServiceInterface $notificationService): Response
    {
        $id  = $request->attributes->get('id');
        $parametre = $request->attributes->get('parametre');
        $demandeTitreCirculation = $entityManager->getRepository(DemandeTitreCirculation::class)->find($id);

        $durationDemande = $demandeTitreCirculation->getIntervention()->getDuree();

        $RepoMailAppli = $entityManager->getRepository(MailAppli::class)->find(1);

        $MailApplication = $RepoMailAppli->getEmail();

        $user = $demandeTitreCirculation->getUser();
        $userEmail = $user->getEmail();

        if (empty($demandeTitreCirculation) === false) {


            if ($parametre === 'instruction') {
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_PENDING);
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Changement de status de votre demande de titre de circulation',
                    template: 'email_status_titre/instruction.html.twig',
                );
            } if ($parametre === 'infocomplementaire') {
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_AWAITING);
            } if ($parametre === 'accord' && $durationDemande === 'temporaire') {
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_PROVISIONED);
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Changement de status de votre demande de titre de circulation',
                    template: 'email_status_titre/accordTemporaire.html.twig',
                );

            } if ($parametre === 'accord' && $durationDemande === 'permanent') {
                $demandeTitreCirculation->setStatus('Accord');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Votre demande de titre de circulation a été accordé',
                    template: 'email_status_titre/accord.html.twig',
                );

            } if ($parametre === 'refus') {
                $demandeTitreCirculation->setStatus('Refusé');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Votre demande de titre de circulation a été refusé',
                    template: 'email_status_titre/refuse.html.twig',
                );
            } if ($parametre === 'edition') {
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_CARD_EDITED);
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Votre demande de titre de circulation a été édité',
                    template: 'email_status_titre/edition.html.twig',
                );
            } if ($parametre === 'delivrer') {
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_CARD_DELIVERED);
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Votre demande de titre de circulation a été délivré',
                    template: 'email_status_titre/delivrer.html.twig',
                );
            }
        }

        $message = $request->request->get('message');

        if ($message) {
            $notificationService->sendTemplatedEmail(
                from: $MailApplication,
                to: $userEmail,
                subject: 'FLUXEL : Il manque des informations dans votre demande de titre de circulation',
                template: 'email_status_titre/infoComplementaire.html.twig',
                templateVars: [
                    'message' => $message,
                ]
            );
        }

        $entityManager->persist($demandeTitreCirculation);
        $entityManager->flush();

        return $this->redirectToRoute('app_demande_sdri', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cartedemandesdri/{parametre}/{id}', name: 'app_demande_sdri_status_carte')]
    public function changementStatusCarte(Request $request, EntityManagerInterface $entityManager, NotificationServiceInterface $notificationService): Response
    {
        $id  = $request->attributes->get('id');
        $parametre = $request->attributes->get('parametre');
        $problemeCarte = $entityManager->getRepository(ProblemeCarte::class)->find($id);

        $RepoMailAppli = $entityManager->getRepository(MailAppli::class)->find(1);

        $MailApplication = $RepoMailAppli->getEmail();

        $user = $problemeCarte->getUser();
        $userEmail = $user->getEmail();

            if ($parametre === 'reedition') {
                $problemeCarte->setStatus('Réédition de carte');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Changement de status de votre problème de carte',
                    template: 'email_status_carte/reedition.html.twig',
                );
            } if ($parametre === 'pret') {
                $problemeCarte->setStatus('Carte prête');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'Votre carte est prête',
                    template: 'email_status_carte/pret.html.twig',
                );
            } if ($parametre === 'supprimer') {
                $problemeCarte->setStatus('Carte supprimer');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'FLUXEL : Changement de status de votre problème de carte',
                    template: 'email_status_carte/supprimer.html.twig',
                );
            } if ($parametre === 'refus') {
                $problemeCarte->setStatus('Refusé');
                $notificationService->sendTemplatedEmail(
                    from: $MailApplication,
                    to: $userEmail,
                    subject: 'Votre problème de carte a été refusé',
                    template: 'email_status_carte/refus.html.twig',
                );
            }

        $entityManager->persist($problemeCarte);
        $entityManager->flush();

        return $this->redirectToRoute('app_demande_sdri', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/demandesdri/importationXML/{parametre}', name: 'app_demande_importXML')]
public function importXML(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
{
    $formData = $request->request->all();
    $selection = $formData['selection'] ?? [];

    if (count($selection) === 0) {
        $this->addFlash(
            'danger',
            'Pour faire un export XML, il faut au minimum avoir sélectionné une demande, dans la catégorie "Titre de circulation".'
        );

        return $this->redirectToRoute('app_demande_sdri');
    }

    $demandes = [];

    foreach ($selection as $id) {
        $demande = $entityManager->getRepository(DemandeTitreCirculation::class)->find($id);

        $titre = $demande->getEtatCivil()->getTitre();

        $sexe = ($titre === 'monsieur') ? 'M' : (($titre === 'madame') ? 'F' : '');
        $titre = ($titre === 'monsieur') ? 'M.' : (($titre === 'madame') ? 'Mme' : 'M');
        $demandes[] = [
            'libelleTitre' => $titre,
            'nom' => $demande->getEtatCivil()->getNom(),
            'prenom' => $demande->getEtatCivil()->getPrenom(),
            'dateNaissance' => $demande->getEtatCivil()->getDateNaissance()->format('d/m/Y'),
            'codePaysNaissance' => $demande->getEtatCivil()->getPaysNaissance(),
            'adresse' => [
                'pointGeo' => $demande->getAdresse()->getTourEtc(),
                'noEtVoie' => $demande->getAdresse()->getNumVoie(),
                'distribution' => $demande->getAdresse()->getDistribution(),
                'codePostal' => $demande->getAdresse()->getCp(),
                'ville' => $demande->getAdresse()->getVille(),
            ],
            'codePaysAdresse' => $demande->getAdresse()->getPays(),
            'coordonnees' => [
                'tel' => $demande->getInfoComplementaire()->getNumTelephone(),
                'email' => $demande->getUser()->getEmail(),
            ],
            'epouseDePersonneNom' => $demande->getEtatCivil()->getNomMarital(),
            'sexe' => $sexe,
            'lieuNaissance' => $demande->getEtatCivil()->getLieuNaissance(),
            'cpNaissance' => $demande->getEtatCivil()->getCpNaissance(),
            'arrondissementNaissance' => $demande->getEtatCivil()->getArrondissementNaissance(),
            'nationalite' => $demande->getEtatCivil()->getNationalite(),
            'identifCompl' => '',
            'enActivite' => 'A',
            'nomPere' => $demande->getFiliation()->getNomPere(),
            'prenomPere' => $demande->getFiliation()->getPrenomPere(),
            'nomMere' => $demande->getFiliation()->getNomMere(),
            'prenomMere' => $demande->getFiliation()->getPrenomMere(),
        ];
    }

    $xmlContent = $serializer->serialize(['personne' => $demandes], 'xml', ['xml_root_node_name' => 'personnes']);

    $dateNom = date('Y-m-d-H-i-s');

    $response = new Response($xmlContent);
    $response->headers->set('Content-Disposition', "attachment; filename=\"$dateNom.xml\"");

    $response->headers->set('Content-Type', 'application/xml');

    return $response;
}


    #[Route('/demandesdri/importationCSV/{parametre}', name: 'app_demande_importCSV')]
    public function importCSV(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $formData = $request->request->all();
        $selection = $formData['selection'] ?? [];

        if (count($selection) === 0 || count($selection) > 30) {
            $this->addFlash('danger', 'Pour effectuer un export CSV, il est nécessaire d\'avoir sélectionné au moins une demande et, au maximum, 30 demandes dans la catégorie "Titre de circulation".');

            return $this->redirectToRoute('app_demande_sdri');
        }

        $data[] = [
            'N°Chrono',
            'AUTORISATION_ACCES',
            '',
            'Nom PIV',
            'Fonction',
            'date d\'entrée',
            'Nom',
            '',
            'Prénom',
            'Sexe',
            'Date de naissance',
            'ville de naissance',
            'code postal ville de naissance',
            'Pays de naissance',
            '',
            'Véhicule',
        ];

        $counter = $entityManager->getRepository(DemandeTitreCirculation::class)->findOneBy([], ['numExport' => 'DESC'])->getNumExport() ?? 0;

        foreach ($selection as $id) {
            $demande = $entityManager->getRepository(DemandeTitreCirculation::class)->find($id);

            if ($demande) {
                $counter++;

                $demande->setNumExport($counter);
                $formattedCounter = sprintf('%04d', $counter);

                $data[] = [
                    'FLUXEL-' . date('Y') . '-' . $formattedCounter,
                    'AUTORISATION_ACCES',
                    '',
                    'PIV_ACC_01',
                    '',
                    '',
                    $demande->getEtatCivil()->getNom(),
                    '',
                    $demande->getEtatCivil()->getPrenom(),
                    $demande->getEtatCivil()->getTitre(),
                    $demande->getEtatCivil()->getDateNaissance()->format('d/m/Y'),
                    $demande->getEtatCivil()->getLieuNaissance(),
                    $demande->getEtatCivil()->getCpNaissance(),
                    $demande->getEtatCivil()->getPaysNaissance(),
                    '',
                    'VL',
                ];

                $entityManager->persist($demande);
                $entityManager->flush();

            }
        }

        $csvEncoder = new CsvEncoder([
            CsvEncoder::DELIMITER_KEY => ';',
            CsvEncoder::ENCLOSURE_KEY => '"',
            CsvEncoder::ESCAPE_CHAR_KEY => '\\',
            CsvEncoder::AS_COLLECTION_KEY => true,
            CsvEncoder::NO_HEADERS_KEY => false,
        ]);

        $csvContent = $serializer->serialize($data, 'csv', [
            'encoder' => $csvEncoder,
        ]);

        $response = new Response($csvContent);

        $dateYear = date('Ymd');

        $fileName = 'FLUXELLAVERA-' . $dateYear . '.csv';

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
