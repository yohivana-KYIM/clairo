<?php

namespace App\Repository;

use App\Entity\EtatCivil;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatCivil>
 *
 * @method EtatCivil|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatCivil|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatCivil[]    findAll()
 * @method EtatCivil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatCivilRepository extends ServiceEntityRepository
{
    public function __construct(protected ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatCivil::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function isPasswordInvalidForUser(User $user, string $password): bool
    {
        $passwordLower = strtolower($password);

            $sql = <<<SQL
        SELECT COUNT(dtc.id) AS count
        FROM demande_titre_circulation dtc
        INNER JOIN etat_civil ec ON dtc.etatcivil_id = ec.id
        WHERE dtc.user_id = :userId
        AND (
            LOWER(:password) LIKE CONCAT('%', LOWER(ec.nom), '%')
            OR LOWER(:password) LIKE CONCAT('%', LOWER(DATE_FORMAT(ec.date_naissance, '%Y-%m-%d')), '%')
        )
    SQL;

// Execute the query
        $result = $this->registry->getConnection()->fetchOne($sql, [
            'userId' => $user,
            'password' => $passwordLower,
        ]);
        
        return $result > 0;
    }
}
