<?php

namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\EntrepriseUnifiee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 *
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    /**
     * Retourne tous les items d'une liste d'entreprises données
     */
    public function findByEntreprises(array $entreprises): array
    {
        if (empty($entreprises)) {
            return [];
        }

        return $this->createQueryBuilder('c')
            ->andWhere('c.entreprise IN (:entreprises)')
            ->setParameter('entreprises', $entreprises)
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne un item spécifique d'une entreprise
     * @throws NonUniqueResultException
     */
    public function findOneByEntrepriseAndProduct(EntrepriseUnifiee $entreprise, int $productId, int $stepId, string $stepType): ?CartItem
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.entreprise = :entreprise')
            ->andWhere('c.stepId = :stepId')
            ->andWhere('c.stepType = :stepType')
            ->andWhere('c.produit = :productId')
            ->setParameters([
                'entreprise' => $entreprise,
                'productId'  => $productId,
                'stepId'     => $stepId,
                'stepType'   => $stepType,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Supprime tous les items d'une entreprise
     */
    public function clearEntrepriseCart(EntrepriseUnifiee $entreprise): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->execute();
    }
}
