<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface as TotpTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface as EmailTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TotpTwoFactorInterface, GoogleTwoFactorInterface, EmailTwoFactorInterface, TrustedDeviceInterface, BackupCodeInterface
{

    public const APP_USER_ID = 182;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $authCode;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $passwordHistory = [];

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isReferentVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DemandeTitreCirculation::class)]
    private Collection $demandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DemandeTitreVehicule::class)]
    private Collection $demandeVehicules;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProblemeCarte::class)]
    private Collection $problemecarte;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $created_at = null;

    #[ORM\Column(type: TYPES::STRING, length: 255, nullable: true, options: [])]
    private ?string $status;

    #[ORM\Column(type: TYPES::INTEGER, options: [
        'default' => 0,
        'unsigned' => true,
    ])]
    private int $trustedVersion;

    #[ORM\Column(name: "totp_secret", type: "string", length: 255, unique: true, nullable: true, options: [])]
    private ?string $totpSecret;

    #[ORM\Column(name: "google_authenticator_secret", type: "string", nullable: true, options: [])]
    private ?string $googleAuthenticatorSecret;


    #[ORM\Column(name: 'mfa_strategies', type: 'json', nullable: true, options: [
        'comment' => 'The MFA strategies enabled for this user. Possible values: email, totp, google',
        'example' => ['email', 'totp', 'google'],
    ])]
    private array $mfaStrategies = ['email'];

    #[ORM\Column(name: "backup_codes", type: "json", nullable: true, options: [
        'comment' => 'The backup codes for this user.',
        'example' => ['123456', '654321'],
        'json' => true
    ])]
    private array $backupCodes = [];

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $receivedMessages;

    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'org')]
    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    private ?Entreprise $entreprise = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $emailAuthCodeGeneratedAt = null;

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;
        return $this;
    }

    public function hasMfaStrategy(string $strategy): bool
    {
        return in_array($strategy, $this->mfaStrategies, true);
    }

    public function addMfaStrategy(string $strategy): self
    {
        if (!in_array($strategy, $this->mfaStrategies, true)) {
            $this->mfaStrategies[] = $strategy;
        }

        return $this;
    }

    public function removeMfaStrategy(string $strategy): self
    {
        $key = array_search($strategy, $this->mfaStrategies, true);
        if ($key !== false) {
            unset($this->mfaStrategies[$key]);
            $this->mfaStrategies = array_values($this->mfaStrategies); // Reindex array to avoid gaps in numeric keys
        }

        return $this;
    }

    public function getPasswordHistory(): array
    {
        return $this->passwordHistory;
    }

    public function setPasswordHistory(array $passwordHistory): void
    {
        $this->passwordHistory = $passwordHistory;
    }

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->demandeVehicules = new ArrayCollection();
        $this->problemecarte = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function removeRole(string $role): static
    {
        $key = array_search($role, $this->roles, true);
        if ($key !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->authCode = null;
        $this->totpSecret = null;
        $this->googleAuthenticatorSecret = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, DemandeTitreCirculation>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    /**
     * @return Collection<int, DemandeTitreVehicule>
     */
    public function getDemandeVehicules(): Collection
    {
        return $this->demandeVehicules;
    }

    public function addDemande(DemandeTitreCirculation $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setUser($this);
        }

        return $this;
    }

    public function removeDemande(DemandeTitreCirculation $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getUser() === $this) {
                $demande->setUser(null);
            }
        }

        return $this;
    }

    public function addDemandeVehicule(DemandeTitreVehicule $demande): static
    {
        if (!$this->demandeVehicules->contains($demande)) {
            $this->demandeVehicules->add($demande);
            $demande->setUser($this);
        }

        return $this;
    }

    public function removeDemandeVehicule(DemandeTitreVehicule $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getUser() === $this) {
                $demande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProblemeCarte>
     */
    public function getProblemecarte(): Collection
    {
        return $this->problemecarte;
    }

    public function addProblemecarte(ProblemeCarte $problemecarte): static
    {
        if (!$this->problemecarte->contains($problemecarte)) {
            $this->problemecarte->add($problemecarte);
            $problemecarte->setUser($this);
        }

        return $this;
    }

    public function removeProblemecarte(ProblemeCarte $problemecarte): static
    {
        if ($this->problemecarte->removeElement($problemecarte)) {
            // set the owning side to null (unless already changed)
            if ($problemecarte->getUser() === $this) {
                $problemecarte->setUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return implode(', ', $this->getRoles());
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function checkMyPass(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @inheritDoc
     */
    public function isTotpAuthenticationEnabled(): bool
    {
        return $this->hasMfaStrategy('totp') && $this->totpSecret;
    }

    /**
     * @inheritDoc
     */
    public function getTotpAuthenticationUsername(): string
    {
        return strstr($this->getEmail(), "@", true);
    }

    /**
     * @inheritDoc
     */
    public function getTotpAuthenticationConfiguration(): ?TotpConfigurationInterface
    {
        return $this->totpSecret !== null
            ? new TotpConfiguration($this->totpSecret, TotpConfiguration::ALGORITHM_SHA1, 30, 6)
            : null;
    }

    /**
     * @return mixed
     */
    public function getTotpSecret(): string
    {
        return $this->totpSecret;
    }

    /**
     * @param mixed $totpSecret
     */
    public function setTotpSecret(string $totpSecret): void
    {
        $this->totpSecret = $totpSecret;
    }

    /**
     * @inheritDoc
     */
    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->hasMfaStrategy('google') && null !== $this->googleAuthenticatorSecret;
    }

    /**
     * @inheritDoc
     */
    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->getTotpAuthenticationUsername();
    }

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void
    {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    /**
     * @inheritDoc
     */
    public function isEmailAuthEnabled(): bool
    {
        return $this->hasMfaStrategy('email');
    }

    /**
     * @inheritDoc
     */
    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function getEmailAuthCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @inheritDoc
     */
    public function setEmailAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
        if ($authCode !== null) {
            $this->markEmailAuthCodeGenerated();
        }
    }

    public function getMfaStrategies(): array
    {
        return $this->mfaStrategies;
    }

    public function setMfaStrategies(array $mfaStrategies): void
    {
        $this->mfaStrategies = $mfaStrategies;
    }

    public function getAuthCode(): ?string
    {
        return $this->authCode;
    }

    public function setAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getTrustedTokenVersion(): int
    {
        return $this->trustedVersion;
    }

    public function setTrustedTokenVersion(int $trustedVersion): void
    {
        $this->trustedVersion = $trustedVersion;
    }

    public function canSetTrustedDevice($user, Request $request, string $firewallName): bool
    {
        return true; // Always allow trusted device feature
    }

    /**
     * Check if it is a valid backup code.
     */
    public function isBackupCode(string $code): bool
    {
        return in_array($code, $this->backupCodes);
    }

    /**
     * Invalidate a backup code
     */
    public function invalidateBackupCode(string $code): void
    {
        $key = array_search($code, $this->backupCodes);
        if ($key !== false){
            unset($this->backupCodes[$key]);
        }
    }

    /**
     * Add a backup code
     */
    public function addBackUpCode(string $backUpCode): void
    {
        if (!in_array($backUpCode, $this->backupCodes)) {
            $this->backupCodes[] = $backUpCode;
        }
    }

    public function isTwoFactorEnabled(): bool
    {
        // Ensure 2FA only activates when an MFA strategy exists and the user is not already fully authenticated
        return !empty($this->mfaStrategies) && !$this->isFullyAuthenticated();
    }

    private function isFullyAuthenticated(): bool
    {
        return isset($_SESSION['_security_main']) && str_contains($_SESSION['_security_main'], 'IS_AUTHENTICATED_FULLY');
    }

    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function setSentMessages(Collection $sentMessages): void
    {
        $this->sentMessages = $sentMessages;
    }

    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function setReceivedMessages(Collection $receivedMessages): void
    {
        $this->receivedMessages = $receivedMessages;
    }

    public function isReferentVerified(): bool
    {
        return $this->isReferentVerified;
    }

    public function setIsReferentVerified(bool $isReferentVerified): void
    {
        $this->isReferentVerified = $isReferentVerified;
    }



    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        if (isset($_SESSION['_sf2_attributes']['active_role'])) {
            return $_SESSION['_sf2_attributes']['active_role'] === $role;
        }

        return in_array($role, $this->roles, true);
    }

    /**
     * Check if the user has a specific demande.
     */
    public function hasDemande(DemandeTitreCirculation $demande): bool
    {
        return $this->demandes->contains($demande);
    }

    /**
     * Check if the user has a specific demande vehicule.
     */
    public function hasDemandeVehicule(DemandeTitreVehicule $demandeVehicule): bool
    {
        return $this->demandeVehicules->contains($demandeVehicule);
    }

    /**
     * Check if the user has a specific probleme carte.
     */
    public function hasProblemeCarte(ProblemeCarte $carte): bool
    {
        return $this->problemecarte->contains($carte);
    }

    /**
     * Check if the user has a specific order.
     */
    public function hasOrder(Order $order): bool
    {
        return $this->orders->contains($order);
    }

    /**
     * Check if the user has sent a specific message.
     */
    public function hasSentMessage(Message $message): bool
    {
        return $this->sentMessages->contains($message);
    }

    /**
     * Check if the user has received a specific message.
     */
    public function hasReceivedMessage(Message $message): bool
    {
        return $this->receivedMessages->contains($message);
    }

    public function markEmailAuthCodeGenerated(): void
    {
        $this->emailAuthCodeGeneratedAt = new \DateTimeImmutable();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function isEmailAuthCodeExpired(int $ttlSeconds = 300): bool
    {
        if (!$this->emailAuthCodeGeneratedAt) return true;
        return $this->emailAuthCodeGeneratedAt->modify("+{$ttlSeconds} seconds") < new \DateTimeImmutable();
    }

    public function clearEmailAuthCode(): void
    {
        $this->authCode = null;
        $this->emailAuthCodeGeneratedAt = null;
    }
}
