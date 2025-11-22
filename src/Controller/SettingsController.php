<?php

namespace App\Controller;

use App\Form\SettingsType;
use App\Repository\SettingRepository;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings/general', name: 'settings_general')]
    public function general(
        Request $request,
        SettingRepository $repo,
        SettingsService $service,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->handleSettings(
            request: $request,
            repo: $repo,
            service: $service,
            groupFilter: 'Paramètres Généraux',
            title: 'Paramètres Généraux'
        );
    }

    #[Route('/settings/personalization', name: 'settings_personalization')]
    public function personalization(
        Request $request,
        SettingRepository $repo,
        SettingsService $service,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->handleSettings(
            request: $request,
            repo: $repo,
            service: $service,
            groupFilter: 'Personnalisation de l\'Interface',
            title: 'Personnalisation'
        );
    }

    #[Route('/settings/security', name: 'settings_security')]
    public function security(
        Request $request,
        SettingRepository $repo,
        SettingsService $service,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->handleSettings(
            request: $request,
            repo: $repo,
            service: $service,
            groupFilter: 'Paramètres de Sécurité',
            title: 'Sécurité'
        );
    }

    private function handleSettings(
        Request $request,
        SettingRepository $repo,
        SettingsService $service,
        string $groupFilter,
        string $title
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $settings = array_filter(
            $repo->findAll(),
            fn ($setting) => $setting->getGroupName() === $groupFilter
        );

        $form = $this->createForm(SettingsType::class, null, ['settings' => $settings]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->updateSettings($form->getData());
            $this->addFlash('success', 'Paramètres enregistrés.');
            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form->createView(),
            'groups' => [$groupFilter => $settings],
            'title' => $title,
        ]);
    }
}
