<?php

namespace App\Repository;

use App\Entity\WalletHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WalletHistory>
 *
 * @method WalletHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method WalletHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method WalletHistory[]    findAll()
 * @method WalletHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WalletHistory::class);
    }

    public function add(WalletHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(WalletHistory $entity, bool $flush = false): void
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
