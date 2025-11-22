<?php

namespace App\Controller\refEntreprise;

use App\Entity\Order;
use App\Entity\User;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Infrastructure\Symfony\Workflow\StepDataWorkflowService;
use App\Service\CartService;
use App\Service\SettingsService;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NotificationServiceInterface $notificationService,
        private readonly StepDataWorkflowService $stepDataWorkflowService,
        private readonly SettingsService $settingsService,
    )
    {
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index(CartService $cart, $stripeSessionId, Request $request): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['StripeSessionId' => $stripeSessionId]);

        $user = $this->getUser();
        assert($user instanceof User);

        $getLastRoute = $request->headers->get('referer');
        $lastRoute = basename((string) $getLastRoute);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState() == 0) {

            foreach ($cart->getTotal() as $item) {

                /** @var StepData $demande */
                $demande = $item['stepData']->getStepData();
                $this->notificationService->sendTemplatedEmail(
                    from: $this->settingsService->get('system_email'),
                    to: $demande->getInternalData('person_step_two', 'employee_email'),
                    subject: 'Bonne nouvelle : votre carte est en préparation',
                    template: 'email_status_titre/employee_commande_succes.html.twig',
                    templateVars: [
                        'nom' => sprintf(
                            '%s %s',
                            $demande->getInternalData('person_step_two', 'employee_first_name'),
                            $demande->getInternalData('person_step_two', 'employee_last_name')
                        ),
                    ]
                );
                $this->stepDataWorkflowService->applyTransition($demande, StepDataWorkflowService::TRANSITION_CONFIRM_PAYMENT);
            }

            $this->notificationService->sendTemplatedEmail(
                from: $this->settingsService->get('system_email'),
                to: $user->getEmail(),
                subject: 'Confirmation de votre commande',
                template: 'email_status_titre/commande_succes.html.twig',
                templateVars: [
                    'order' => $order,
                ]
            );

            $this->em->flush();
            $cart->clear();

            // modifier le statut isPaid mettre à 1
            $order->setIsPaid(1);
            $order->setState(1);
        }

        return $this->render('refEntreprise/order_success/index.html.twig', [
            'order' => $order,
            'lastRoute' => $lastRoute
        ]);
    }
}
