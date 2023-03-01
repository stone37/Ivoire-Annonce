<?php

namespace App\Repository;

use App\Entity\ThreadMessageMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ThreadMessageMetadata>
 *
 * @method ThreadMessageMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThreadMessageMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThreadMessageMetadata[]    findAll()
 * @method ThreadMessageMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadMessageMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadMessageMetadata::class);
    }

    public function add(ThreadMessageMetadata $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ThreadMessageMetadata $entity, bool $flush = false): void
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
