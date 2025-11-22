<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\User\RegistrationService;
use App\Service\Email\EmailVerificationService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(name: 'app_register_')]
class RegistrationController extends AbstractController
{
    private readonly RegistrationService $registrationService;
    private readonly EmailVerificationService $emailVerificationService;

    public function __construct(RegistrationService $registrationService, EmailVerificationService $emailVerificationService)
    {
        $this->registrationService = $registrationService;
        $this->emailVerificationService = $emailVerificationService;
    }

    #[Route('/register', name: 'form', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registrationService->registerUser($user, $form);

            $this->addFlash(
                'success',
                'Pour finaliser votre inscription, veuillez cliquer sur le lien inclus dans l\'e-mail que nous vous avons envoyé.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'verify_email', methods: ['GET'])]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        try {
            $this->emailVerificationService->verifyEmail($request);

            $this->addFlash('success', 'Votre adresse email a bien été vérifiée, vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        } catch (Exception $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getMessage(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register_form');
        }
    }
}
