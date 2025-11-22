<?php

// src/EventSubscriber/RedirectIfNotVerifiedSubscriber.php

namespace App\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectIfNotVerifiedSubscriber implements EventSubscriberInterface
{
    private $security;
    private $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Skip if it's a subrequest
        if (!$event->isMainRequest()) {
            return;
        }

        // List of routes to ignore (e.g. login, register, homepage)
        $excludedRoutes = [
            'app_index', 'app_home', 'app_login',
            'app_logout', 'app_register', 'app_user_submit_company',
            'app_referent_validate', 'app_referent_reject', 'app_api_referent_autocomplete',
            '2fa_login', '2fa_login_check', '2fa_login_confirm', '2fa_login_confirm_check', 'app_autocomplete_sirene'
        ];

        $route = $request->attributes->get('_route');

        if (in_array($route, $excludedRoutes)) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user) {
            return; // no user, let firewall handle redirection
        }

        // Add your logic here
        if (
            !$user->isVerified() || !$user->isReferentVerified()
        ) {
            $homeUrl = $this->router->generate('app_home');
            $event->setResponse(new RedirectResponse($homeUrl));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
        ];
    }
}
