<?php

namespace App\Service\Validator\Classes;

use App\Entity\User;
use App\Repository\EtatCivilRepository;
use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Strategy for validating that the password does not contain personal information.
 */
#[AutoconfigureTag('validator.strategy')]
class NoPersonalInfoStrategy implements PassValidatorStrategyInterface
{
    private array $errors;

    public function __construct(private readonly Security $security, private readonly EtatCivilRepository $etatCivilRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function validate(string $password): bool
    {
        $this->flushErrors();
        $user = $this->security->getUser();
        if (!($user instanceof User)) return true;

        if ($this->etatCivilRepository->isPasswordInvalidForUser($user, $password)) {
            $this->addError('Votre mot de passe ne doit pas contenir vos informations personnelles (nom, date de naissance)');
            return false;
        }

        return true;
    }

    public function getEncounteredErrors(): array
    {
        return $this->errors;
    }

    public function flushErrors(): void
    {
        $this->errors = [];
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}
