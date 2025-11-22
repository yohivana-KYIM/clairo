<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Produit;
use App\Entity\User;
use App\MultiStepBundle\Entity\PersonFlattenedStepData;
use App\MultiStepBundle\Entity\VehicleFlattenedStepData;
use App\Repository\CartItemRepository;
use App\Repository\EntrepriseUnifieeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    private EntityManagerInterface $em;
    private CartItemRepository $cartRepo;
    private EntrepriseUnifieeRepository $entrepriseRepo;
    private Security $security;

    public function __construct(
        EntityManagerInterface $em,
        CartItemRepository $cartRepo,
        EntrepriseUnifieeRepository $entrepriseRepo,
        Security $security
    ) {
        $this->em = $em;
        $this->cartRepo = $cartRepo;
        $this->entrepriseRepo = $entrepriseRepo;
        $this->security = $security;
    }

    private function getCurrentUser(): User
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new LogicException("Utilisateur non connecté.");
        }
        return $user;
    }

    private function getEntrepriseForUser(User $user): array
    {
        $email = $user->getEmail();

        return $this->entrepriseRepo->createQueryBuilder('e')
            ->where('e.emailReferent = :email OR e.suppleant1 = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    private function getEntreprisesBySiret(string $siret, string $siren, string $naf): array
    {

        return $this->entrepriseRepo->createQueryBuilder('e')
            ->where('e.siret = :siret AND e.siren = :siren AND e.naf = :naf')
            ->setParameter('siret', $siret)
            ->setParameter('siren', $siren)
            ->setParameter('naf', $naf)
            ->getQuery()
            ->getResult();
    }

    public function add(int $productId, int $stepId, string $stepType = 'person'): void
    {
        $nom = '';
        $prenom = '';
        $stepData = null;
        if ($stepType === 'person') {
            $stepData = $this->em->getRepository(PersonFlattenedStepData::class)->find($stepId);
            $prenom = $stepData?->getEmployeeFirstName() ?? '';
            $nom    = $stepData?->getEmployeeLastName() ?? '';
        } elseif ($stepType === 'vehicle') {
            $stepData = $this->em->getRepository(VehicleFlattenedStepData::class)->find($stepId);
            $nom    = $stepData?->getResponsibleName() ?? '';
        }
        if (!$stepData) return;
        $entreprises = $this->getEntreprisesBySiret($stepData->getSiret() ?? '', $stepData->getSiren() ?? '', $stepData->getNaf() ?? '');

        foreach ($entreprises as $entreprise) {
            $produit = $this->em->getRepository(Produit::class)->find($productId);
            if (!$produit) {
                throw new InvalidArgumentException("Produit $productId introuvable.");
            }

            $item = $this->cartRepo->findOneByEntrepriseAndProduct($entreprise, $productId, $stepId, $stepType);

            if ($item) {
                $item->increment();
            } else {
                $item = (new CartItem())
                    ->setProduit($produit)
                    ->setEntreprise($entreprise)
                    ->setStepId($stepId)
                    ->setStepType($stepType)
                    ->setNom($nom)
                    ->setPrenom($prenom)
                    ->setQuantity(1);
                $this->em->persist($item);
            }
        }

        $this->em->flush();
    }

    public function get(): array
    {
        $user = $this->getCurrentUser();
        $entreprises = $this->getEntrepriseForUser($user);

        return $this->cartRepo->findByEntreprises($entreprises);
    }

    public function clear(): void
    {
        $user = $this->getCurrentUser();
        $entreprises = $this->getEntrepriseForUser($user);

        foreach ($entreprises as $entreprise) {
            $this->cartRepo->clearEntrepriseCart($entreprise);
        }
    }

    public function delete(int $productId, int $stepId, string $stepType = 'person'): void
    {
        $user = $this->getCurrentUser();
        $entreprises = $this->getEntrepriseForUser($user);

        foreach ($entreprises as $entreprise) {
            $item = $this->cartRepo->findOneByEntrepriseAndProduct($entreprise, $productId, $stepId, $stepType);

            if ($item) {
                if ($item->getQuantity() > 1) {
                    $item->decrement();
                } else {
                    $this->em->remove($item);
                }
                $this->em->flush();
            }
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getTotal(): array
    {
        $user = $this->getCurrentUser();
        $entreprises = $this->getEntrepriseForUser($user);

        $items = $this->cartRepo->findByEntreprises($entreprises);
        $cartData = [];

        foreach ($items as $item) {
            $stepData = null;
            if ($item->getStepType() === 'person') {
                $stepData = $this->em->getRepository(PersonFlattenedStepData::class)->find($item->getStepId());
            } elseif ($item->getStepType() === 'vehicle') {
                $stepData = $this->em->getRepository(VehicleFlattenedStepData::class)->find($item->getStepId());
            }

            $cartData[] = [
                'produit'   => $item->getProduit(),
                'demandeId' => $item->getStepId(),
                'stepType'  => $item->getStepType(),
                'quantity'  => $item->getQuantity(),
                'nom'       => $item->getNom(),
                'prenom'    => $item->getPrenom(),
                'stepData'  => $stepData,
                'entreprise'=> $item->getEntreprise(),
            ];
        }

        return $cartData;
    }
}
