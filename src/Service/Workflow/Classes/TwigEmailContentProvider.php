<?php

namespace App\Service\Workflow\Classes;

use App\Entity\User;
use App\Service\Workflow\Interfaces\EmailContentProviderInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigEmailContentProvider implements EmailContentProviderInterface
{
    private readonly Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getEmailContent(User $user): string
    {
        return $this->twig->render('workflow/emails/notification.html.twig', [
            'user' => $user,
        ]);
    }
}
