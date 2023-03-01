<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alert>
 *
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function add(Alert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Alert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getEnabledByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = 1')
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('a.createdAt', 'desc');
    }

    public function getByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.enabled = 0')
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $user)
            ->orderBy('a.createdAt', 'desc');
    }
}
