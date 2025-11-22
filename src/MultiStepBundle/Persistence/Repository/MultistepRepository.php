<?php
namespace App\MultiStepBundle\Persistence\Repository;

use App\Entity\User;
use App\MultiStepBundle\Domain\Vehicule\AbstractVehicleStep;
use App\MultiStepBundle\Entity\StepData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StepData>
 *
 * @method StepData|null find($id, $lockMode = null, $lockVersion = null)
 * @method StepData|null findOneBy(array $criteria, array $orderBy = null)
 * @method StepData[]    findAll()
 * @method StepData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MultistepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StepData::class);
    }

    /**
     * Creates the base query for filtering by statuses.
     */
    private function createBaseQuery(array $statuses): QueryBuilder
    {
        return $this->createQueryBuilder('d')
            ->where('d.status IN (:statuses)')
            ->setParameter('statuses', $statuses);
    }

    /**
     * Get all demandes filtered by statuses.
     */
    public function findByStatuses(array $statuses): array
    {
        return $this->createBaseQuery($statuses)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndStepName($user, string $stepNumber): ?StepData
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->andWhere('LOWER(s.stepNumber) = :stepNumber')
            ->setParameter('user', $user)
            ->setParameter('stepNumber', strtolower($stepNumber))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllStepNamesForUser($user): array
    {
        return $this->createQueryBuilder('s')
            ->select('LOWER(s.stepNumber) as name')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findAccessStepsForUser(User $user, ?string $type = null, ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.stepType=:type')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)
            ->setParameter('type', $type);

        if ($limit !== null) $qb->setMaxResults($limit);
        if ($offset !== null) $qb->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }
}