<?php

namespace App\Repository;

use App\Entity\AdvertRead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdvertRead>
 *
 * @method AdvertRead|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvertRead|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvertRead[]    findAll()
 * @method AdvertRead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertReadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvertRead::class);
    }

    public function add(AdvertRead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdvertRead $entity, bool $flush = false): void
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
}
