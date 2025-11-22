<?php

declare(strict_types=1);

namespace App\Controller;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh; 
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleAuthenticatorTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface as TotpTwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class QrCodeController extends AbstractController
{
    private string $serverName;
    private string $rootPath;
    private string $image;

    public function __construct(ParameterBagInterface $params, KernelInterface $kernel)
    {
        $this->serverName = $params->get('scheb_two_factor.totp.server_name');
        $this->image = $params->get('scheb_two_factor.totp.parameters')['image'];
        $this->rootPath = $kernel->getProjectDir();
    }

    #[Route('/settings/qr/ga', name: 'qr_code_ga')]
    public function displayGoogleAuthenticatorQrCode(TokenStorageInterface $tokenStorage, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->displayGoogleAuthenticatorQrCodeForUser($tokenStorage->getToken()->getUser(), $googleAuthenticator);
    }

    #[Route('/settings/qr/totp', name: 'qr_code_totp')]
    public function displayTotpQrCode(TokenStorageInterface $tokenStorage, TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->displayTotpQrCodeForUser($tokenStorage->getToken()->getUser(), $totpAuthenticator);
    }

    #[Route('/settings/qr/ga/{user}', name: 'qr_code_ga_user')]
    public function displayGoogleAuthenticatorQrCodeForUser(UserInterface $user, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        if (!($user instanceof GoogleAuthenticatorTwoFactorInterface)) {
            throw new NotFoundHttpException('Cannot display QR code');
        }

        return $this->displayQrCode($googleAuthenticator->getQRContent($user));
    }

    #[Route('/settings/qr/totp/{user}', name: 'qr_code_totp_user')]
    public function displayTotpQrCodeForUser(UserInterface $user, TotpAuthenticatorInterface $totpAuthenticator): Response
    {
        if (!($user instanceof TotpTwoFactorInterface)) {
            throw new NotFoundHttpException('Cannot display QR code');
        }

        return $this->displayQrCode($totpAuthenticator->getQRContent($user));
    }

    private function displayQrCode(string $qrCodeContent): Response
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($qrCodeContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(0)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }

    public function getServerName(): string
    {
        return $this->serverName;
    }

    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
