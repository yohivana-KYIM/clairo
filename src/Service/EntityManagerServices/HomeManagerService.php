<?php

namespace App\Service\EntityManagerServices;

use App\Entity\HistoriqueLogin;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeManagerService
{

    private readonly SessionInterface $session;

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RequestStack $requestStack)
    {
        $this->session = $this->requestStack->getSession();
    }

    public function getUserLatestRequest(User $user)
    {
        return $user->getDemandes()->last();
    }

    public function handleLoginHistory(Request $request, User $user): void
    {
        // Récupération du referer et du path
        $referer = (string) $request->headers->get('referer', '');
        $path = parse_url($referer, PHP_URL_PATH) ?? '';

        // Vérifie si la requête vient d'une étape d'authentification (login ou 2FA)
        $comesFromAuthStep = false;

        // Cas 1 : le referer contient /login ou /2fa
        if (preg_match('#/(login|2fa|two[-_]?factor)(/|$)#i', $path)) {
            $comesFromAuthStep = true;
        }

        // Cas 2 : le nom de la route courante correspond aussi à une étape d'authentification
        $route = (string) $request->attributes->get('_route', '');
        if (preg_match('#(login|2fa|two[-_]?factor)#i', $route)) {
            $comesFromAuthStep = true;
        }


        if ($comesFromAuthStep && !$this->session->has('login_info')) {
            $this->session->set('login_info', 'Informations spécifiques à la page de connexion');

            $history = new HistoriqueLogin();
            $history->setIp($request->getClientIp());
            $history->setLogin($user->getEmail());
            $history->setCreatedAt(new DateTime());

            $this->entityManager->persist($history);
            $this->entityManager->flush();
        }
    }
}
