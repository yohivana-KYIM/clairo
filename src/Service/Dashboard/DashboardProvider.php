<?php
// =====================================================================
// FILE: src/Service/Dashboard/DashboardProvider.php
// =====================================================================


namespace App\Service\Dashboard;


use App\Entity\Dashboard\{DashboardAdminIndicators,DashboardRefsecuIndicators,DashboardSdriIndicators,DashboardUserIndicators,
    AlertsAdmin,AlertsRefsecu,AlertsSdri,AlertsUser,
    TodoAdmin,TodoRefsecu,TodoSdri,TodoUser,
    DeadlinesAdmin,DeadlinesRefsecu,DeadlinesSdri,DeadlinesUser,
    RankingCompanyRequests,RankingCompanyRefusals,RankingCompanyMissingDocs,RankingUserActivity,RankingSdriValidations,
    RatioCompanyPerformance,RatioUserActivity,RatioMonthlyApproval,RatioDocumentCompleteness,
    ChartRequestsMonthlyStatus,ChartDecisionEvolution,
    GardienPersonSteps};
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class DashboardProvider
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly TranslatorInterface $translator,
    ) {

    }
    /**
     * Build a role-aware dashboard payload consumable by Twig.
     *
     * Structure:
     * [
     * 'widgets' => [ [ 'id' => '...', 'title' => '...', 'type' => 'kpis|chart|table', 'data' => mixed, 'cols' => 1..4 ], ... ],
     * 'roles' => [...]
     * ]
     */
    public function buildFor(UserInterface $user): array
    {
        $roles = $user->getRoles();
        $widgets = [];

        $widgets = array_merge($widgets, $this->userWidgets($user));

        if ($this->hasRole($roles, 'ROLE_ADMIN')) {
            $widgets = array_merge($widgets, $this->adminWidgets());
        }
        if ($this->hasRole($roles, 'ROLE_REFSECU')) {
            $widgets = array_merge($widgets, $this->refsecuWidgets());
        }
        if ($this->hasRole($roles, 'ROLE_SDRI')) {
            $widgets = array_merge($widgets, $this->sdriWidgets());
        }
        if ($this->hasRole($roles, 'ROLE_GARDIEN'))  { $widgets = array_merge($widgets, $this->gardienWidgets()); }

        // De-dup & tri
        $dedup = [];
        foreach ($widgets as $w) { $dedup[$w['id']] = $w; }


        // Sort: KPIs -> Charts -> Tables
        $order = ['kpis' => 0, 'chart' => 1, 'ratio' => 1, 'table' => 2];
        $widgets = array_values($dedup);
        usort($widgets, function($a,$b) use ($order) {
            return ($order[$a['type']] ?? 99) <=> ($order[$b['type']] ?? 99);
        });

        return [
            'widgets' => $widgets,
            'roles' => $roles,
        ];
    }

    private function hasRole(array $roles, string $needle): bool
    {
        return in_array($needle, $roles, true);
    }

    private function adminWidgets(): array
    {

        $kpis = $this->repo(DashboardAdminIndicators::class)->findAll();
        $kpi = $kpis[0] ?? null;

        $chartRows = $this->repo(ChartRequestsMonthlyStatus::class)->findAll();
        $chart = $this->buildMonthlyStatusChartData($chartRows);

        $todos = $this->repo(TodoAdmin::class)->createQueryBuilder('v')
            ->setMaxResults(12)->getQuery()->getResult();
        $alerts = $this->repo(AlertsAdmin::class)->createQueryBuilder('v')
            ->setMaxResults(12)->getQuery()->getResult();


        $rankRequests = $this->repo(RankingCompanyRequests::class)->top(3);
        $rankRefusals = $this->repo(RankingCompanyRefusals::class)->top(3);
        $rankMissing = $this->repo(RankingCompanyMissingDocs::class)->top(3);
        $deadlines = $this->repo(DeadlinesAdmin::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();


        $ratioCompany = $this->repo(RatioCompanyPerformance::class)->createQueryBuilder('v')
            ->setMaxResults(20)->getQuery()->getResult();

        // 👉 NOUVEAU : RatioMonthlyApproval → chart (taux %)
        $ratioMonthlyRows = $this->repo(RatioMonthlyApproval::class)->createQueryBuilder('v')
            ->orderBy('v.month', 'ASC')->getQuery()->getResult();
        $ratioMonthlyChart = $this->buildMonthlyApprovalChartData($ratioMonthlyRows);

        // 👉 NOUVEAU : ChartDecisionEvolution → chart (par décision)
        $decisionRows   = $this->repo(ChartDecisionEvolution::class)->findAll();
        $decisionChart  = $this->buildDecisionEvolutionChartData($decisionRows);

        // 👉 NOUVEAU : RatioDocumentCompleteness → table
        $ratioDoc = $this->repo(RatioDocumentCompleteness::class)->createQueryBuilder('v')
            ->orderBy('v.completenessRate', 'DESC')
            ->setMaxResults(15)
            ->getQuery()->getResult();


        return [
            [ 'id' => 'admin-kpis', 'title' => 'Indicateurs (Admin)', 'type' => 'kpis', 'cols' => 2, 'data' => $kpi ],
            [ 'id' => 'chart-monthly-status', 'title' => 'Requêtes mensuelles par statut', 'type' => 'chart', 'cols' => 2, 'data' => $chart ],

            // 🔥 nouveaux widgets
            [ 'id' => 'chart-approval-rate', 'title' => 'Taux d’approbation mensuel', 'type' => 'chart', 'cols' => 2, 'data' => $ratioMonthlyChart ],
            [ 'id' => 'chart-decision-evolution', 'title' => 'Évolution des décisions d’accès', 'type' => 'chart', 'cols' => 2, 'data' => $decisionChart ],

            [ 'id' => 'admin-todos', 'title' => 'À faire (Admin)', 'type' => 'table', 'cols' => 2, 'data' => $todos, 'columns' => ['stepId','companyName','status','priorityLevel','requestDate','todoType'] ],
            [ 'id' => 'admin-alerts', 'title' => 'Alertes (Admin)', 'type' => 'table', 'cols' => 2, 'data' => $alerts, 'columns' => ['stepId','companyName','status','daysOpen','alertType','requestDate'] ],
            [ 'id' => 'ranking-requests', 'title' => 'Top entreprises par demandes', 'type' => 'table', 'cols' => 2, 'data' => $rankRequests, 'columns' => ['companyName','totalRequests'] ],
            [ 'id' => 'ranking-refusals', 'title' => 'Top refus', 'type' => 'table', 'cols' => 2, 'data' => $rankRefusals, 'columns' => ['companyName','refusals'] ],
            [ 'id' => 'ranking-missing', 'title' => 'Docs manquants', 'type' => 'table', 'cols' => 2, 'data' => $rankMissing, 'columns' => ['companyName','incompleteRequests'] ],
            [ 'id' => 'ratio-company', 'title' => 'Performance entreprises', 'type' => 'table', 'cols' => 2, 'data' => $ratioCompany, 'columns' => ['companyName','approvalRate','refusalRate','approved','refused','totalRequests'] ],

            // table des ratios de complétude
            [ 'id' => 'ratio-doc-completeness', 'title' => 'Complétude des dossiers (entreprise)', 'type' => 'table', 'cols' => 2, 'data' => $ratioDoc, 'columns' => ['companyName','totalRequests','completeRequests','completenessRate'] ],

            [ 'id' => 'admin-deadlines', 'title' => 'Échéances contrats', 'type' => 'table', 'cols' => 2, 'data' => $deadlines, 'columns' => ['stepId','companyName','employeeFirstName','employeeLastName','daysLeftContract','daysLeftAccess'] ],
        ];
    }

    private function refsecuWidgets(): array
    {
        $kpi = ($this->repo(DashboardRefsecuIndicators::class)->findAll())[0] ?? null;
        $todos = $this->repo(TodoRefsecu::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();
        $alerts = $this->repo(AlertsRefsecu::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();
        $deadlines = $this->repo(DeadlinesRefsecu::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();


        return [
            [ 'id' => 'refsecu-kpis', 'title' => 'Indicateurs (Réf. Sécu)', 'type' => 'kpis', 'cols' => 2, 'data' => $kpi ],
            [ 'id' => 'refsecu-todos', 'title' => 'À faire (Réf. Sécu)', 'type' => 'table', 'cols' => 2, 'data' => $todos, 'columns' => ['stepId','companyName','status','priorityLevel','todoType'] ],
            [ 'id' => 'refsecu-alerts', 'title' => 'Alertes (Réf. Sécu)', 'type' => 'table', 'cols' => 2, 'data' => $alerts, 'columns' => ['stepId','companyName','status','alertType','requestDate'] ],
            [ 'id' => 'refsecu-deadlines', 'title' => 'Échéances contrats', 'type' => 'table', 'cols' => 2, 'data' => $deadlines, 'columns' => ['stepId','companyName','employeeFirstName','employeeLastName','daysLeftContract','daysLeftAccess'] ],
        ];
    }

    private function sdriWidgets(): array
    {
        $kpi = ($this->repo(DashboardSdriIndicators::class)->findAll())[0] ?? null;
        $todos = $this->repo(TodoSdri::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();
        $alerts = $this->repo(AlertsSdri::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();
        $deadlines = $this->repo(DeadlinesSdri::class)->createQueryBuilder('v')->setMaxResults(12)->getQuery()->getResult();
        $validators = $this->repo(RankingSdriValidations::class)->findAll();


        return [
            [ 'id' => 'sdri-kpis', 'title' => 'Indicateurs (SDRI)', 'type' => 'kpis', 'cols' => 2, 'data' => $kpi ],
            [ 'id' => 'sdri-todos', 'title' => 'À faire (SDRI)', 'type' => 'table', 'cols' => 2, 'data' => $todos, 'columns' => ['stepId','companyName','status','priorityLevel','todoType'] ],
            [ 'id' => 'sdri-alerts', 'title' => 'Blocages/Alertes (SDRI)', 'type' => 'table', 'cols' => 2, 'data' => $alerts, 'columns' => ['stepId','companyName','status','alertType'] ],
            [ 'id' => 'sdri-deadlines', 'title' => 'Échéances formations', 'type' => 'table', 'cols' => 2, 'data' => $deadlines, 'columns' => ['stepId','companyName','employeeEmail','daysLeftTraining'] ],
            [ 'id' => 'sdri-validators', 'title' => 'Top validateurs', 'type' => 'table', 'cols' => 2, 'data' => $validators, 'columns' => ['securityOfficerName','approvedCount'] ],
        ];
    }

    private function userWidgets(UserInterface $user): array
    {
        $email = method_exists($user, 'getEmail') ? (string)$user->getEmail() : null;


        $kpi = ($this->repo(DashboardUserIndicators::class)->findAll())[0] ?? null;
        $todos = $this->repo(TodoUser::class)->createQueryBuilder('v')
            ->andWhere('v.employeeEmail IS NULL OR v.employeeEmail = :me')
            ->setParameter('me', $email)
            ->setMaxResults(12)->getQuery()->getResult();


        $alerts = $this->repo(AlertsUser::class)->createQueryBuilder('v')
            ->andWhere('v.employeeEmail IS NULL OR v.employeeEmail = :me')
            ->setParameter('me', $email)
            ->setMaxResults(12)->getQuery()->getResult();


        $recent = $this->repo(\App\Entity\Dashboard\RecentUserRequests::class)->createQueryBuilder('v')
            ->andWhere('v.employeeEmail IS NULL OR v.employeeEmail = :me')
            ->setParameter('me', $email)
            ->setMaxResults(12)->getQuery()->getResult();


        $deadlines = $this->repo(DeadlinesUser::class)->createQueryBuilder('v')
            ->andWhere('v.employeeEmail IS NULL OR v.employeeEmail = :me')
            ->setParameter('me', $email)
            ->setMaxResults(12)->getQuery()->getResult();


// User-specific activity ratio (filter by email when possible)
        $ratioUser = $this->repo(RatioUserActivity::class)->createQueryBuilder('v')
            ->andWhere('v.employeeEmail = :me')
            ->setParameter('me', $email)
            ->getQuery()->getResult();


        return [
            [ 'id' => 'user-kpis', 'title' => 'Mes indicateurs', 'type' => 'kpis', 'cols' => 2, 'data' => $kpi ],
            [ 'id' => 'user-recent', 'title' => 'Mes demandes récentes', 'type' => 'table', 'cols' => 2, 'data' => $recent, 'columns' => ['stepId','status','requestDate','daysSince','priority'] ],
            [ 'id' => 'user-alerts', 'title' => 'Mes alertes', 'type' => 'table', 'cols' => 2, 'data' => $alerts, 'columns' => ['stepId','status','alertType','requestDate'] ],
            [ 'id' => 'user-todos', 'title' => 'Mes actions à faire', 'type' => 'table', 'cols' => 2, 'data' => $todos, 'columns' => ['stepId','status','priorityLevel','todoType'] ],
            [ 'id' => 'user-deadlines', 'title' => 'Mes échéances', 'type' => 'table', 'cols' => 2, 'data' => $deadlines, 'columns' => ['stepId','contractEndDate','fluxelTrainingDate','daysUntilContractEnd','daysUntilTrainingExpire'] ],
            [ 'id' => 'user-activity', 'title' => 'Mon taux d\'approbation', 'type' => 'table', 'cols' => 2, 'data' => $ratioUser, 'columns' => ['employeeEmail','approvalRate','approved','refused','total'] ],
        ];
    }

    private function gardienWidgets(): array
    {
        $rows = $this->repo(GardienPersonSteps::class)->createQueryBuilder('v')
            ->setMaxResults(20)->getQuery()->getResult();
        return [
            [ 'id' => 'gardien-last', 'title' => 'Dernières cartes éditées/livrées', 'type' => 'table', 'cols' => 2, 'data' => $rows, 'columns' => ['stepId','companyName','employeeFirstName','employeeLastName','status','requestDate'] ],
        ];
    }

    private function repo(string $entityClass)
    {
        return $this->registry->getRepository($entityClass);
    }


    /** @param ChartRequestsMonthlyStatus[] $rows */
    private function buildMonthlyStatusChartData(array $rows): array
    {
        // (existant, inchangé)
        $months = [];
        $statuses = [];
        foreach ($rows as $r) {
            $months[$r->getMonth()] = true;
            $statuses[$r->getStatus()] = true;
        }
        $labels = array_values(array_keys($months));
        sort($labels);
        $statusKeys = array_values(array_keys($statuses));
        sort($statusKeys);

        $matrix = [];
        foreach ($statusKeys as $s) {
            $matrix[$s] = array_fill(0, count($labels), 0);
        }

        $indexByMonth = array_flip($labels);
        foreach ($rows as $r) {
            $m = $r->getMonth();
            $s = $r->getStatus();
            $i = $indexByMonth[$m] ?? null;
            if ($i !== null) { $matrix[$s][$i] = (int)$r->getCount(); }
        }
        $datasets = [];
        foreach ($statusKeys as $s) { $datasets[] = [ 'label' => $this->translator->trans($s), 'data' => $matrix[$s] ]; }

        return [ 'labels' => $labels, 'datasets' => $datasets ];
    }

    /** @param RatioMonthlyApproval[] $rows */
    private function buildMonthlyApprovalChartData(array $rows): array
    {
        // X = mois, Y = taux % (0..100)
        $labels = [];
        $rate   = [];
        foreach ($rows as $r) {
            $labels[] = $r->getMonth();
            $rate[]   = round($r->getApprovalRate() * 100, 1); // en %
        }
        sort($labels);
        // Réindexer les valeurs selon labels triés
        $map = [];
        foreach ($rows as $r) { $map[$r->getMonth()] = round($r->getApprovalRate() * 100, 1); }
        $rate = array_map(fn($m) => $map[$m] ?? 0, $labels);

        return [
            'labels' => $labels,
            'datasets' => [
                [ 'label' => 'Taux d’approbation (%)', 'data' => $rate ]
            ],
        ];
    }

    /** @param ChartDecisionEvolution[] $rows */
    private function buildDecisionEvolutionChartData(array $rows): array
    {
        // X = mois, une série par accessDecision
        $months = [];
        $decisions = [];
        foreach ($rows as $r) {
            $months[$r->getMonth()] = true;
            $decisions[$r->getAccessDecision()] = true;
        }
        $labels = array_values(array_keys($months)); sort($labels);
        $keys   = array_values(array_keys($decisions)); sort($keys);

        $indexByMonth = array_flip($labels);
        $matrix = [];
        foreach ($keys as $k) { $matrix[$k] = array_fill(0, count($labels), 0); }

        foreach ($rows as $r) {
            $i = $indexByMonth[$r->getMonth()] ?? null;
            if ($i !== null) {
                $matrix[$r->getAccessDecision()][$i] = (int)$r->getCount();
            }
        }

        $datasets = [];
        foreach ($keys as $k) { $datasets[] = [ 'label' => $this->translator->trans($k), 'data' => $matrix[$k] ]; }

        return [ 'labels' => $labels, 'datasets' => $datasets ];
    }
}