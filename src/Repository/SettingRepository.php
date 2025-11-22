<?php

// src/Repository/SettingRepository.php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    /**
     * Persists and flushes the Setting entity.
     */
    public function save(Setting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Deletes the Setting entity.
     */
    public function remove(Setting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Flush all pending changes.
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Returns settings grouped by group name.
     *
     * @return array<string, Setting[]>
     */
    public function findAllGrouped(): array
    {
        $results = $this->findAll();
        $groups = [];

        foreach ($results as $setting) {
            $groups[$setting->getGroupName()][] = $setting;
        }

        return $groups;
    }
}
