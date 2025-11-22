<?php

namespace App\Controller;

use App\Entity\User;
use App\MultiStepBundle\Application\Enum\StepDataStatus;
use App\MultiStepBundle\Entity\StepData;
use App\Repository\EntrepriseRepository;
use App\Repository\UserRepository;
use App\Security\EncryptionService;
use App\Service\EntityManagerServices\HomeManagerService;
use App\Service\NameGuesser;
use App\Service\SettingsService;
use App\Service\UserValidationTokenManager;
use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;
use App\Service\Workflow\Classes\NotificationService;
use App\Repository\UserValidationTokenRepository;
use DateMalformedStringException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/', name: 'app_')]
class HomeController extends AbstractController
{
    public function __construct(
        private readonly HomeManagerService $homeManager,
        private readonly EncryptionService $encryptionService,
        private readonly SettingsService $settingsService,
        private readonly TranslatorInterface $translator,
        private readonly UserValidationTokenManager $tokenManager,
        private readonly UserValidationTokenRepository $tokenRepository,
        #[Autowire('%env(APP_SECRET)%')] public readonly string $appSecret
    ) {}

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'home')]
    public function home(Request $request, PassValidatorStrategyInterface $passValidatorStrategy): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->homeManager->getUserLatestRequest($user);
        $this->homeManager->handleLoginHistory($request, $user);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'demandeTitreCirculation' => $demandeTitreCirculation,
            'passValidator' => $passValidatorStrategy,
            'decodedPassword' => $this->encryptionService->decrypt($user->getPassword())
        ]);
    }

    /**
     * @throws DateMalformedStringException
     * @throws TransportExceptionInterface
     */
    #[Route('/user/submit-company', name: 'user_submit_company', methods: ['POST'])]
    public function submitCompany(
        Request $request,
        EntrepriseRepository $entrepriseRepository,
        EntityManagerInterface $em,
        NotificationService $notificationService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $companySiret = preg_replace('/\s+/', '', $request->request->get('company'));
        if (!$companySiret) {
            $this->addFlash('error', 'Veuillez sélectionner une entreprise.');
            return $this->redirectToRoute('app_home');
        }

        $entreprise = $entrepriseRepository->findOneBy(['siret' => $companySiret]) ?? $entrepriseRepository->findOneBy(['siret' => $request->request->get('company')]) ?? $entrepriseRepository->findOneBy(['siret' => trim($request->request->get('company'))]);
        if (!$entreprise) {
            $this->addFlash('error', 'Entreprise introuvable.');
            return $this->redirectToRoute('app_home');
        }

        $user->setEntreprise($entreprise);
        $em->persist($user);

        $expiresAt = (new DateTime())->modify('+7 days');
        $validateToken = $this->tokenManager->generate($user, 'validate', $expiresAt);
        $rejectToken = $this->tokenManager->generate($user, 'reject', $expiresAt);
        $this->tokenManager->flush();

        $referentEmail = $entreprise->getEmailReferent();
        $suppleants = array_filter([$entreprise->getSuppleant1(), $entreprise->getSuppleant2()]);

        $sdriToEmails = null;
        if ($this->settingsService->get('sdri_receive_refsec_email')) {
            $sdriToEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
            $sdriCcEmails = explode(',', $this->settingsService->get('sdri_team_cc_emails'));
            $sdriToEmails = reset($sdriToEmails);
            $sdriCcEmails = reset($sdriCcEmails);
        }

        if ($referentEmail) {
            $notificationService->sendTemplatedEmail(
                from: new Address($this->settingsService->get('system_email'), $this->translator->trans('refsec_validate_user_mail_title')),
                to: $sdriToEmails ?? $referentEmail,
                subject: $this->translator->trans('refsec_validate_user_mail_subject'),
                cc: $sdriCcEmails ?? $suppleants,
                template: 'emails/validation_referent.html.twig',
                templateVars: [
                    'user' => $user,
                    'entreprise' => $entreprise,
                    'validate_token' => $validateToken->getToken(),
                    'reject_token' => $rejectToken->getToken(),
                    'app_url' => rtrim($this->generateUrl('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL), '/')
                ]
            );
        }

        $this->addFlash('success', 'Votre entreprise a été enregistrée. Un référent sera notifié pour valider votre compte.');
        return $this->redirectToRoute('app_home');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/referent/validate/{userId}/{token}', name: 'referent_validate')]
    public function validateUser(
        int $userId,
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        NotificationService $notificationService
    ): Response {
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $validationToken = $this->tokenRepository->findOneBy([
            'user' => $user,
            'token' => $token,
            'type' => 'validate',
            'used' => false,
        ]);


        if (!$validationToken || $validationToken->getExpiresAt() < new DateTime()) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        $roles = $user->getRoles();
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        $user->setRoles($roles);
        $user->setIsReferentVerified(true);
        $validationToken->setUsed(true);

        $em->persist($user);
        $em->persist($validationToken);
        $em->flush();

        $notificationService->sendAppNotification(
            $user,
            '✅ Votre inscription a été validée par votre référent. Vous avez désormais accès à toutes les fonctionnalités.'
        );

        $notificationService->sendTemplatedEmail(
            from: new Address($this->settingsService->get('system_email'), $this->translator->trans('refsec_validate_user_mail_title')),
            to: $user->getEmail(),
            subject: $this->translator->trans('refsec_validated_user_subscription'),
            template: 'emails/user_validated.html.twig',
            templateVars: ['user' => $user]
        );

        $this->addFlash('success', 'Utilisateur validé.');
        return $this->redirectToRoute('app_home');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/referent/reject/{userId}/{token}', name: 'referent_reject')]
    public function rejectUser(
        int $userId,
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        NotificationService $notificationService
    ): Response {
        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $rejectionToken = $this->tokenRepository->findOneBy([
            'user' => $user,
            'token' => $token,
            'type' => 'reject',
            'used' => false,
        ]);

        if (!$rejectionToken || $rejectionToken->getExpiresAt() < new DateTime()) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        $user->setEntreprise(null);
        $user->setIsReferentVerified(false);
        $rejectionToken->setUsed(true);

        $em->persist($user);
        $em->persist($rejectionToken);
        $em->flush();

        // ✅ Envoyer notification UI interne (App)
        $notificationService->sendAppNotification(
            $user,
            '❌ Votre inscription a été refusée par votre référent. Veuillez contacter votre administrateur pour plus de détails.'
        );

        // ✅ Envoyer email
        $notificationService->sendTemplatedEmail(
            from: new Address($this->settingsService->get('system_email'), $this->translator->trans('refsec_validate_user_mail_title')),
            to: $user->getEmail(),
            subject: $this->translator->trans('refsec_rejected_user_subscription'),
            template: 'emails/user_rejected.html.twig',
            templateVars: ['user' => $user]
        );

        $this->addFlash('info', 'Inscription refusée.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/api/referent/{siret}', name: 'api_referent_autocomplete', methods: ['GET'])]
    public function referentAutocomplete(
        SettingsService $settingsService,
        EntrepriseRepository $entrepriseRepository,
        NameGuesser $nameGuesser,
        string $siret
    ): JsonResponse {
        $entreprise = $entrepriseRepository->findOneBy(['siret' => $siret]);

        $sdriToEmails = $sdriCcEmails = null;
        if ($this->settingsService->get('sdri_receive_refsec_email')) {
            $sdriToEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
            $sdriCcEmails = explode(',', $this->settingsService->get('sdri_team_cc_emails'));
            $sdriToEmails = reset($sdriToEmails);
            $sdriCcEmails = reset($sdriCcEmails);
        }

        if (!$entreprise) {
            return $this->json(['error' => 'Entreprise non trouvée'], 404);
        }

        $email = $entreprise->getEmailReferent();
        $nomResponsable = $entreprise->getNomResponsable();

        if (!$nomResponsable && $email) {
            $nomResponsable = $nameGuesser->guessName($email);
        }

        $emailSup = $entreprise->getSuppleant1() ?? $entreprise->getSuppleant2();
        $nomSuppleant = $entreprise->getNomSuppleant1() ?? $entreprise->getNomSuppleant2();

        if (!$nomSuppleant && $emailSup) {
            $nomSuppleant = $nameGuesser->guessName($emailSup);
        }

        return $this->json([
            'responsable' => [
                'name' => $nomResponsable,
                'position' => 'Référent Sûreté',
                'email' => $email,
                'phone' => $entreprise->getTelephoneReferent()
            ],
            'suppleant' => [
                'name' => $nomSuppleant,
                'position' => 'Suppleant Sûreté',
                'email' => $emailSup,
                'phone' => $entreprise->getTelephoneSuppleant1() ?? $entreprise->getTelephoneSuppleant2(),
            ]
        ]);
    }

    #[Route('/cards/{id?}', name: 'cards', defaults: ['id' => null])]
    public function cards(?StepData $stepData = null): ?Response
    {
        if (!$stepData) return new Response();
        if ($stepData->getStatus() != StepDataStatus::PAID) return new Response();

        return $this->render('card.html.twig', ['cards' => $stepData->generateCards()]);
    }
}
