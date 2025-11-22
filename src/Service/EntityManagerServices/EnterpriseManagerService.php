<?php

namespace App\Service\EntityManagerServices;

use App\Entity\Entreprise;
use App\Entity\AdresseEntreprise;
use App\Repository\EntrepriseRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EnterpriseManagerService
{
    private readonly EntityManagerInterface $entityManager;
    private readonly EntrepriseRepository $entrepriseRepository;
    private readonly ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntrepriseRepository $entrepriseRepository,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->entrepriseRepository = $entrepriseRepository;
        $this->validator = $validator;
    }

    /**
     * Creates a new Entreprise with its associated AdresseEntreprise.
     *
     * @param Entreprise $entreprise
     * @param AdresseEntreprise $adresse
     * @return Entreprise
     */
    public function createEntreprise(Entreprise $entreprise, AdresseEntreprise $adresse): Entreprise
    {
        $this->validateEntreprise($entreprise);

        $entreprise->setAdresse($adresse);
        $entreprise->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($adresse);
        $this->entityManager->persist($entreprise);
        $this->entityManager->flush();

        return $entreprise;
    }

    /**
     * Updates an existing Entreprise.
     *
     * @param Entreprise $entreprise
     */
    public function updateEntreprise(Entreprise $entreprise): void
    {
        $this->validateEntreprise($entreprise);

        $this->entityManager->persist($entreprise);
        $this->entityManager->flush();
    }

    /**
     * Deletes an Entreprise.
     *
     * @param Entreprise $entreprise
     */
    public function deleteEntreprise(Entreprise $entreprise): void
    {
        $this->entityManager->remove($entreprise);
        $this->entityManager->flush();
    }

    /**
     * Retrieves all Entreprises.
     *
     * @return array
     */
    public function getAllEntreprises(): array
    {
        return $this->entrepriseRepository->findAll();
    }

    /**
     * Validates an Entreprise entity.
     *
     * @param Entreprise $entreprise
     */
    private function validateEntreprise(Entreprise $entreprise): void
    {
        $violations = $this->validator->validate($entreprise->getNom(), [
            new Assert\NotBlank(['message' => 'Entreprise name cannot be blank']),
            new Assert\Length(['max' => 255, 'maxMessage' => 'Entreprise name cannot exceed 255 characters']),
        ]);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getMessage();
            }

            throw new InvalidArgumentException(implode(', ', $errorMessages));
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySiren(string $siren): ?Entreprise
    {
        return $this->entrepriseRepository->createQueryBuilder('e')
            ->andWhere('e.siren = :siren')
            ->setParameter('siren', $siren)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySiret(string $siret): ?Entreprise
    {
        return $this->entrepriseRepository->createQueryBuilder('e')
            ->andWhere('e.siren = :siret')
            ->setParameter('siret', $siret)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFiliales(Entreprise $entreprise): array
    {
        return $this->entrepriseRepository->createQueryBuilder('e')
            ->andWhere('e.entrepriseMere = :mère')
            ->setParameter('mère', $entreprise)
            ->getQuery()
            ->getResult();
    }

    public function findGroupHierarchy(Entreprise $root): array
    {
        $qb = $this->entrepriseRepository->createQueryBuilder('e')
            ->leftJoin('e.entrepriseMere', 'em')
            ->addSelect('em')
            ->where('e.entrepriseMere = :root')
            ->setParameter('root', $root);

        return $qb->getQuery()->getResult();
    }
}
