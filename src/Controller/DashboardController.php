<?php

namespace App\Controller;

use App\Entity\DemandeTitreCirculation;
use App\Entity\Entreprise;
use App\Entity\User;
use App\Repository\Dashboard\AlertsUserRepository;
use App\Repository\Dashboard\ChartDecisionEvolutionRepository;
use App\Repository\Dashboard\ChartRequestsMonthlyStatusRepository;
use App\Repository\Dashboard\DashboardUserIndicatorsRepository;
use App\Repository\Dashboard\DeadlinesUserRepository;
use App\Repository\Dashboard\RankingCompanyRequestsRepository;
use App\Service\Dashboard\DashboardProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private DashboardProvider $provider
    ) {

    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $payload = $this->provider->buildFor($this->getUser());


        return $this->render('dashboard/index.html.twig', [
            'payload' => $payload,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/dashboard/user', name: 'dashboard_user', methods: ['GET'])]
    public function userDashboard(DashboardUserIndicatorsRepository $repo): Response
    {
        $ind = $repo->one();

        // Fallback if the view returns no row (e.g., empty dataset)
        $stats = $ind ? [
            'drafts'            => $ind->getDrafts(),
            'in_progress'       => $ind->getInProgress(),
            'approved'          => $ind->getApproved(),
            'refused'           => $ind->getRefused(),
            'missing_documents' => $ind->getMissingDocuments(),
        ] : [
            'drafts' => 0, 'in_progress' => 0, 'approved' => 0, 'refused' => 0, 'missing_documents' => 0,
        ];

        return $this->render('dashboard/user.html.twig', ['stats' => $stats]);
    }

    #[Route('/dashboard/chart/status', name: 'dashboard_chart_status', methods: ['GET'])]
    public function statusChart(
        ChartRequestsMonthlyStatusRepository $repo,
        Request $request
    ): Response {
        // Filtre optionnel : ?from=YYYY-MM&to=YYYY-MM
        $from = $request->query->get('from');
        $to   = $request->query->get('to');

        if ($from && $to) {
            $rows = $repo->findByMonthRange($from, $to);
        } else {
            $rows = $repo->all();
        }

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'month'  => $row->getMonth(),
                'status' => $this->translator->trans($row->getStatus()),
                'count'  => $row->getCount(),
            ];
        }

        return $this->render('dashboard/charts/status.html.twig', [
            'data' => $data,
            'from' => $from,
            'to'   => $to,
        ]);
    }

    #[Route('/dashboard/chart/decision', name: 'dashboard_chart_decision')]
    public function decisionChart(
        ChartDecisionEvolutionRepository $repo,
        Request $request
    ): Response {
        // 1. Récupération de la décision depuis les paramètres de requête GET
        $decision = $request->query->get('decision', 'approved');

        // 2. Requête des données depuis la vue SQL
        $data = $repo->forDecision($decision);

        // 3. Rendu du template Twig
        return $this->render('dashboard/charts/decision.html.twig', [
            'data' => $data,
            'selected_decision' => $decision,
        ]);
    }

    #[Route('/dashboard/ranking/companies', name: 'dashboard_ranking_companies')]
    public function topCompanies(RankingCompanyRequestsRepository $repo): Response
    {
        $ranking = $repo->top();
        return $this->render('dashboard/ranking/companies.html.twig', [
            'ranking' => $ranking,
        ]);
    }

    #[Route('/dashboard/alerts/user', name: 'dashboard_alerts_user')]
    public function userAlerts(
        AlertsUserRepository $repo,
    ): Response {
        $email = $this->getUser()?->getEmail();
        $alerts = $repo->forUser($email);

        return $this->render('dashboard/alerts/user.html.twig', [
            'alerts' => $alerts,
            'email' => $email,
        ]);
    }

    #[Route('/dashboard/deadlines/user', name: 'dashboard_deadlines_user')]
    public function userDeadlines(DeadlinesUserRepository $repo): Response
    {
        $email = $this->getUser()->getEmail();
        $deadlines = $repo->forUser($email);

        return $this->render('dashboard/deadlines/user.html.twig', [
            'deadlines' => $deadlines,
        ]);
    }
}